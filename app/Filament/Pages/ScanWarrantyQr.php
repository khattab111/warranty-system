<?php

namespace App\Filament\Pages;

use App\Models\Warranty;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ScanWarrantyQr extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.scan-warranty-qr';

    protected static ?string $title = 'مسح QR Code';

    public ?string $scannedToken = null;

    public ?Warranty $scannedWarranty = null;

    public bool $showForm = false;

    public bool $showInfo = false;

    public string $errorMessage = '';

    public string $manualToken = '';

    /**
     * حالة نموذج Filament.
     *
     * @var array<string, mixed>
     */
    public array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'مسح QR Code';
    }

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-camera';
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'scan-warranty-qr';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                TextInput::make('device_type')
                    ->label('نوع الجهاز')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('مثال: iPhone 15 Pro'),

                TextInput::make('imei')
                    ->label('IMEI')
                    ->required()
                    ->rule('digits:15')
                    ->placeholder('15 رقماً'),

                DateTimePicker::make('warranty_expires_at')
                    ->label('تاريخ انتهاء الضمان')
                    ->required()
                    ->after('today')
                    ->displayFormat('Y-m-d H:i')
                    ->seconds(false),
            ]);
    }

    public function processScan(string $scannedData): void
    {
        $this->resetError();

        $this->scannedToken = null;
        $this->scannedWarranty = null;
        $this->showForm = false;
        $this->showInfo = false;

        try {
            $token = $this->extractToken($scannedData);

            if (! $token) {
                $this->errorMessage =
                    'رمز QR غير صالح. يرجى مسح رمز صحيح.';

                return;
            }

            $warranty = Warranty::query()
                ->where('public_token', $token)
                ->first();

            if (! $warranty) {
                $this->errorMessage =
                    'هذا الرمز غير موجود في النظام.';

                return;
            }

            $this->scannedToken = $token;
            $this->scannedWarranty = $warranty;
            $this->showForm = true;

            /*
             * نعرض المعلومات الموجودة سواء كان الضمان
             * مفعلاً أو غير مفعل.
             */
            $this->showInfo =
                $warranty->device_type !== null ||
                $warranty->imei !== null ||
                $warranty->warranty_expires_at !== null;

            $this->form->fill([
                'device_type' => $warranty->device_type,
                'imei' => $warranty->imei,
                'warranty_expires_at' => $warranty->warranty_expires_at,
            ]);
        } catch (\Throwable $exception) {
            $this->errorMessage =
                'حدث خطأ أثناء معالجة الرمز.';

            Log::error('Warranty scan failed', [
                'scanned_data' => $scannedData,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);
        }
    }

    public function activate(): void
    {
        if (! $this->scannedWarranty) {
            Notification::make()
                ->title('لم يتم تحديد ضمان')
                ->danger()
                ->send();

            return;
        }

        /*
         * إذا كان مفعلًا بالفعل، نحوله إلى تحديث
         * بدل إظهار خطأ.
         */
        if ($this->scannedWarranty->activated_at !== null) {
            $this->updateWarranty();

            return;
        }

        try {
            $data = Warranty::validateActivationData(
                $this->form->getState(),
                $this->scannedWarranty->id,
            );
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('بيانات التفعيل غير صالحة')
                ->body($exception->validator->errors()->first())
                ->danger()
                ->send();

            return;
        }

        try {
            DB::transaction(function () use ($data): void {
                $this->scannedWarranty->update([
                    'device_type' => $data['device_type'],
                    'imei' => $data['imei'],
                    'warranty_expires_at' =>
                        $data['warranty_expires_at'],

                    'activated_at' => now(),
                ]);
            });

            $this->scannedWarranty->refresh();

            $this->showForm = true;
            $this->showInfo = true;

            /*
             * نعيد تعبئة الحقول من قاعدة البيانات
             * بعد الحفظ.
             */
            $this->fillWarrantyForm();

            Log::info('Warranty activated', [
                'warranty_id' => $this->scannedWarranty->id,
                'user_id' => auth()->id(),
            ]);

            Notification::make()
                ->title('تم تفعيل الضمان بنجاح')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Log::error('Warranty activation failed', [
                'warranty_id' => $this->scannedWarranty->id,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            Notification::make()
                ->title('تعذر تفعيل الضمان')
                ->body('حدث خطأ أثناء حفظ بيانات الضمان.')
                ->danger()
                ->send();
        }
    }

    public function updateWarranty(): void
    {
        if (! $this->scannedWarranty) {
            Notification::make()
                ->title('لم يتم تحديد ضمان')
                ->danger()
                ->send();

            return;
        }

        try {
            $data = Warranty::validateActivationData(
                $this->form->getState(),
                $this->scannedWarranty->id,
            );
        } catch (ValidationException $exception) {
            Notification::make()
                ->title('بيانات التحديث غير صالحة')
                ->body($exception->validator->errors()->first())
                ->danger()
                ->send();

            return;
        }

        try {
            DB::transaction(function () use ($data): void {
                $this->scannedWarranty->update([
                    'device_type' => $data['device_type'],
                    'imei' => $data['imei'],
                    'warranty_expires_at' =>
                        $data['warranty_expires_at'],
                ]);
            });

            $this->scannedWarranty->refresh();

            $this->showInfo = true;

            $this->fillWarrantyForm();

            Log::info('Warranty updated via scan', [
                'warranty_id' => $this->scannedWarranty->id,
                'user_id' => auth()->id(),
            ]);

            Notification::make()
                ->title('تم تحديث معلومات الضمان بنجاح')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Log::error('Warranty update via scan failed', [
                'warranty_id' => $this->scannedWarranty->id,
                'user_id' => auth()->id(),
                'exception' => $exception,
            ]);

            Notification::make()
                ->title('تعذر تحديث بيانات الضمان')
                ->body('حدث خطأ أثناء حفظ التعديلات.')
                ->danger()
                ->send();
        }
    }

    public function manualLookup(): void
    {
        $this->manualToken = trim($this->manualToken);

        if ($this->manualToken === '') {
            $this->errorMessage =
                'يرجى إدخال رمز QR أو رابط الضمان.';

            return;
        }

        $this->processScan($this->manualToken);
    }

    public function resetScan(): void
    {
        $this->scannedToken = null;
        $this->scannedWarranty = null;
        $this->showForm = false;
        $this->showInfo = false;
        $this->errorMessage = '';
        $this->manualToken = '';
        $this->data = [];

        $this->form->fill();
    }

    private function fillWarrantyForm(): void
    {
        if (! $this->scannedWarranty) {
            $this->form->fill();

            return;
        }

        $this->form->fill([
            'device_type' => $this->scannedWarranty->device_type,
            'imei' => $this->scannedWarranty->imei,
            'warranty_expires_at' =>
                $this->scannedWarranty->warranty_expires_at,
        ]);
    }

    private function extractToken(string $data): ?string
    {
        $data = trim($data);

        $patterns = [
            '/\/warranty\/([a-f0-9-]{36})(?:[\/?#]|$)/i',
            '/([a-f0-9]{8}-[a-f0-9]{4}-[1-5][a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $data, $matches)) {
                return strtolower($matches[1]);
            }
        }

        return null;
    }

    private function resetError(): void
    {
        $this->errorMessage = '';
    }
}
