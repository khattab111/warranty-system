<?php

namespace App\Filament\Pages;

use App\Models\Warranty;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class GenerateQrCodes extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.generate-qr-codes';

    protected static ?string $title = 'توليد رموز QR';

    public int $quantity = 10;

    /**
     * القائمة المعروضة حاليًا داخل الصفحة.
     *
     * @var Collection<int, Warranty>|null
     */
    public ?Collection $generatedWarranties = null;

    /**
     * الرموز المحددة للطباعة أو التنزيل.
     *
     * @var array<int>
     */
    public array $selectedForPrint = [];

    public static function getNavigationLabel(): string
    {
        return 'توليد رموز QR';
    }

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-qr-code';
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'generate-qr-codes';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'quantity' => 'العدد المطلوب',
        ];
    }

    public function mount(): void
    {
        $this->loadAvailableWarranties();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('quantity')
                    ->label('العدد المطلوب')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(500)
                    ->required()
                    ->helperText('الحد الأدنى 1 والحد الأعلى 500 رمز في العملية الواحدة.'),
            ]);
    }

    public function generate(): void
    {
        $validated = $this->validate();

        $quantity = (int) $validated['quantity'];

        try {
            $warranties = DB::transaction(function () use ($quantity): Collection {
                $timestamp = now();
                $records = [];

                for ($index = 0; $index < $quantity; $index++) {
                    $records[] = [
                        'public_token' => (string) Str::uuid(),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                Warranty::insert($records);

                return Warranty::query()
                    ->whereIn(
                        'public_token',
                        array_column($records, 'public_token')
                    )
                    ->orderBy('id')
                    ->get();
            });

            $this->generatedWarranties = $warranties;

            $this->selectedForPrint = $warranties
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            Log::info('QR warranties generated', [
                'quantity' => $quantity,
                'user_id' => auth()->id(),
            ]);

            Notification::make()
                ->title("تم إنشاء {$quantity} رمز QR بنجاح")
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Log::error('Failed to generate QR warranties', [
                'quantity' => $quantity,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            Notification::make()
                ->title('حدث خطأ أثناء إنشاء الرموز')
                ->body('يرجى المحاولة مرة أخرى أو مراجعة سجل الأخطاء.')
                ->danger()
                ->send();
        }
    }

    public function refreshList(): void
    {
        $this->loadAvailableWarranties();

        Notification::make()
            ->title('تم تحديث القائمة')
            ->success()
            ->send();
    }

    public function selectAll(): void
    {
        if ($this->generatedWarranties?->isEmpty() !== false) {
            return;
        }

        $this->selectedForPrint = $this->generatedWarranties
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public function clearSelection(): void
    {
        $this->selectedForPrint = [];
    }

    public function downloadQr(Warranty $warranty)
    {
        try {
            $this->ensureWarrantyIsVisible($warranty);

            $result = $this->generateQrCode($warranty);
            $fileName = $this->qrFileName($warranty);

            $this->markWarrantiesAsPrinted([$warranty->id]);
            $this->refreshDisplayedRecords();

            return response($result->getString())
                ->header('Content-Type', 'image/png')
                ->header(
                    'Content-Disposition',
                    'attachment; filename="'.$fileName.'"'
                );
        } catch (\Throwable $exception) {
            $this->reportExportFailure(
                'Failed to download individual QR code',
                $exception,
                ['warranty_id' => $warranty->id]
            );

            return null;
        }
    }

    public function downloadZip(): ?BinaryFileResponse
    {
        $warranties = $this->currentWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoWarranties();

            return null;
        }

        return $this->exportZip(
            $warranties,
            'qr-codes'
        );
    }

    public function downloadSelectedZip(): ?BinaryFileResponse
    {
        $warranties = $this->selectedWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoSelection();

            return null;
        }

        return $this->exportZip(
            $warranties,
            'qr-codes-selected'
        );
    }

    public function downloadPdf(): ?StreamedResponse
    {
        $warranties = $this->currentWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoWarranties();

            return null;
        }

        return $this->exportPdf(
            $warranties,
            'qr-codes'
        );
    }

    public function downloadSelectedPdf(): ?StreamedResponse
    {
        $warranties = $this->selectedWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoSelection();

            return null;
        }

        return $this->exportPdf(
            $warranties,
            'qr-codes-selected'
        );
    }

    public function markAllAsPrinted(): void
    {
        $warranties = $this->currentWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoWarranties();

            return;
        }

        $this->markWarrantiesAsPrinted(
            $warranties->pluck('id')->all()
        );

        $this->refreshDisplayedRecords();

        Notification::make()
            ->title('تم تعيين جميع الرموز كمطبوعة')
            ->success()
            ->send();
    }

    public function markSelectedAsPrinted(): void
    {
        $warranties = $this->selectedWarranties();

        if ($warranties->isEmpty()) {
            $this->notifyNoSelection();

            return;
        }

        $this->markWarrantiesAsPrinted(
            $warranties->pluck('id')->all()
        );

        $this->refreshDisplayedRecords();

        Notification::make()
            ->title('تم تعيين الرموز المحددة كمطبوعة')
            ->success()
            ->send();
    }

    private function loadAvailableWarranties(): void
    {
        $this->generatedWarranties = Warranty::query()
            ->whereNull('activated_at')
            ->latest('created_at')
            ->limit(100)
            ->get();

        $this->selectedForPrint = [];
    }

    /**
     * @return Collection<int, Warranty>
     */
    private function currentWarranties(): Collection
    {
        if (! $this->generatedWarranties instanceof Collection) {
            return new Collection();
        }

        $ids = $this->generatedWarranties
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($ids === []) {
            return new Collection();
        }

        return Warranty::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, Warranty>
     */
    private function selectedWarranties(): Collection
    {
        if (
            empty($this->selectedForPrint) ||
            ! $this->generatedWarranties instanceof Collection
        ) {
            return new Collection();
        }

        $visibleIds = $this->generatedWarranties
            ->pluck('id')
            ->map(fn ($id): int => (int) $id);

        $selectedIds = collect($this->selectedForPrint)
            ->map(fn ($id): int => (int) $id)
            ->intersect($visibleIds)
            ->unique()
            ->values()
            ->all();

        if ($selectedIds === []) {
            return new Collection();
        }

        return Warranty::query()
            ->whereIn('id', $selectedIds)
            ->orderBy('id')
            ->get();
    }

    private function ensureWarrantyIsVisible(Warranty $warranty): void
    {
        $isVisible = $this->generatedWarranties
            ?->contains(
                fn (Warranty $item): bool =>
                    (int) $item->id === (int) $warranty->id
            ) ?? false;

        abort_unless($isVisible, 403);
    }

    /**
     * @param Collection<int, Warranty> $warranties
     */
    private function exportZip(
        Collection $warranties,
        string $prefix
    ): ?BinaryFileResponse {
        Storage::disk('local')->makeDirectory('temporary/qr');

        $fileName = $prefix.'-'.now()->format('Y-m-d-H-i-s').'.zip';
        $relativePath = 'temporary/qr/'.$fileName;
        $absolutePath = Storage::disk('local')->path($relativePath);

        $zip = new ZipArchive();

        try {
            $openResult = $zip->open(
                $absolutePath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ($openResult !== true) {
                throw new \RuntimeException(
                    'ZIP creation failed with status: '.$openResult
                );
            }

            foreach ($warranties as $warranty) {
                $result = $this->generateQrCode($warranty);

                $zip->addFromString(
                    $this->qrFileName($warranty),
                    $result->getString()
                );
            }

            $zip->close();

            $this->markWarrantiesAsPrinted(
                $warranties->pluck('id')->all()
            );

            $this->refreshDisplayedRecords();

            return response()
                ->download($absolutePath, $fileName)
                ->deleteFileAfterSend(true);
        } catch (\Throwable $exception) {
            if ($zip->status === ZipArchive::ER_OK) {
                $zip->close();
            }

            Storage::disk('local')->delete($relativePath);

            $this->reportExportFailure(
                'Failed to export QR ZIP file',
                $exception,
                ['warranty_ids' => $warranties->pluck('id')->all()]
            );

            return null;
        }
    }

    /**
     * @param Collection<int, Warranty> $warranties
     */
    private function exportPdf(
        Collection $warranties,
        string $prefix
    ): ?StreamedResponse {
        try {
            $qrCodes = $warranties
                ->map(function (Warranty $warranty): array {
                    $result = $this->generateQrCode($warranty);

                    return [
                        'image' => 'data:image/png;base64,'.
                            base64_encode($result->getString()),

                        'reference' => $warranty->short_reference,
                    ];
                })
                ->all();

            $pdf = Pdf::loadView('pdf.qr-codes', [
                'qrCodes' => $qrCodes,
            ])->setPaper('a4', 'portrait');

            /*
             * إنشاء المحتوى أولًا.
             * إذا فشل DomPDF لن يتم تحديث حالة الطباعة.
             */
            $output = $pdf->output();

            $this->markWarrantiesAsPrinted(
                $warranties->pluck('id')->all()
            );

            $this->refreshDisplayedRecords();

            $fileName = $prefix.'-'.now()->format('Y-m-d-H-i-s').'.pdf';

            return response()->streamDownload(
                static function () use ($output): void {
                    echo $output;
                },
                $fileName,
                [
                    'Content-Type' => 'application/pdf',
                ]
            );
        } catch (\Throwable $exception) {
            $this->reportExportFailure(
                'Failed to export QR PDF file',
                $exception,
                ['warranty_ids' => $warranties->pluck('id')->all()]
            );

            return null;
        }
    }

    private function markWarrantiesAsPrinted(array $ids): void
    {
        if ($ids === []) {
            return;
        }

        Warranty::query()
            ->whereIn('id', $ids)
            ->whereNull('printed_at')
            ->update([
                'printed_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function refreshDisplayedRecords(): void
    {
        if (! $this->generatedWarranties instanceof Collection) {
            return;
        }

        $ids = $this->generatedWarranties->pluck('id')->all();

        $this->generatedWarranties = Warranty::query()
            ->whereIn('id', $ids)
            ->orderByDesc('created_at')
            ->get();
    }

    private function generateQrCode(Warranty $warranty): ResultInterface
    {
        $url = route(
            'warranty.show',
            ['public_token' => $warranty->public_token]
        );

        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 320,
            margin: 12,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new PngWriter())->write($qrCode);
    }

    private function qrFileName(Warranty $warranty): string
    {
        $reference = preg_replace(
            '/[^A-Za-z0-9_-]/',
            '-',
            (string) $warranty->short_reference
        );

        $reference = trim((string) $reference, '-');

        if ($reference === '') {
            $reference = (string) $warranty->id;
        }

        return 'qr-'.$reference.'.png';
    }

    private function notifyNoWarranties(): void
    {
        Notification::make()
            ->title('لا توجد رموز متاحة')
            ->warning()
            ->send();
    }

    private function notifyNoSelection(): void
    {
        Notification::make()
            ->title('لم يتم تحديد أي رمز')
            ->body('حدد رمزًا واحدًا على الأقل ثم أعد المحاولة.')
            ->warning()
            ->send();
    }

    private function reportExportFailure(
        string $message,
        \Throwable $exception,
        array $context = []
    ): void {
        Log::error($message, array_merge($context, [
            'user_id' => auth()->id(),
            'exception' => $exception,
        ]));

        Notification::make()
            ->title('تعذر تجهيز الملف')
            ->body('حدث خطأ أثناء تجهيز الملف المطلوب.')
            ->danger()
            ->send();
    }
}
