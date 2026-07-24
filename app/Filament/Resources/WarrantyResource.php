<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarrantyResource\Pages;
use App\Models\Warranty;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class WarrantyResource extends Resource
{
    protected static ?string $model = Warranty::class;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationLabel(): string
    {
        return 'الضمانات';
    }

    public static function getModelLabel(): string
    {
        return 'ضمان';
    }

    public static function getPluralModelLabel(): string
    {
        return 'الضمانات';
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'warranties';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('معلومات الجهاز')
                    ->schema([
                        TextInput::make('device_type')
                            ->label('نوع الجهاز')
                            ->maxLength(150)
                            ->required(fn ($record) => $record?->activated_at !== null)
                            ->placeholder('مثال: iPhone 15 Pro'),
                        TextInput::make('imei')
                            ->label('IMEI')
                            ->unique(ignoreRecord: true)
                            ->rule('digits:15')
                            ->required(fn ($record) => $record?->activated_at !== null)
                            ->placeholder('15 رقماً'),
                        DateTimePicker::make('warranty_expires_at')
                            ->label('تاريخ انتهاء الضمان')
                            ->required(fn ($record) => $record?->activated_at !== null)
                            ->after('today'),
                    ]),
                Section::make('معلومات الرمز')
                    ->schema([
                        TextInput::make('public_token')
                            ->label('الرمز العام')
                            ->disabled()
                            ->dehydrated(false),
                        DateTimePicker::make('activated_at')
                            ->label('تاريخ التفعيل')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_reference')
                    ->label('المرجع')
                    ->searchable(false)
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('public_token')
                    ->label('الرمز العام')
                    ->searchable()
                    ->placeholder('--'),
                Tables\Columns\TextColumn::make('device_type')
                    ->label('نوع الجهاز')
                    ->searchable()
                    ->placeholder('غير محدد'),
                Tables\Columns\TextColumn::make('imei')
                    ->label('IMEI')
                    ->searchable()
                    ->placeholder('غير محدد'),
                Tables\Columns\TextColumn::make('warranty_expires_at')
                    ->label('انتهاء الضمان')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->placeholder('غير محدد'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'inactive' => 'غير مفعل',
                        'active' => 'ساري',
                        'expired' => 'منتهي',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'inactive' => 'gray',
                        'active' => 'success',
                        'expired' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('workflow_status')
                    ->label('مرحلة العمل')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unprinted_unlinked' => 'غير مطبوع / غير مرتبط',
                        'printed_pending' => 'مطبوع / غير مرتبط',
                        'linked_completed' => 'مرتب / مكتمل',
                        default => 'غير معروف',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'unprinted_unlinked' => 'gray',
                        'printed_pending' => 'warning',
                        'linked_completed' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('activated_at')
                    ->label('تاريخ التفعيل')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('--'),
                Tables\Columns\TextColumn::make('printed_at')
                    ->label('تاريخ الطباعة')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('لم يُطبع')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d H:i') : 'غير مطبوع'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('inactive')
                    ->label('غير المفعلة')
                    ->query(fn (Builder $query) => $query->whereNull('activated_at')),
                Filter::make('active')
                    ->label('السارية')
                    ->query(fn (Builder $query) => $query->whereNotNull('activated_at')->where('warranty_expires_at', '>', now())),
                Filter::make('expired')
                    ->label('المنتهية')
                    ->query(fn (Builder $query) => $query->whereNotNull('activated_at')->where('warranty_expires_at', '<=', now())),
                Filter::make('expiring_7')
                    ->label('تنتهي خلال 7 أيام')
                    ->query(fn (Builder $query) => $query->whereNotNull('activated_at')
                        ->where('warranty_expires_at', '>', now())
                        ->where('warranty_expires_at', '<=', now()->addDays(7))),
                Filter::make('expiring_30')
                    ->label('تنتهي خلال 30 يوماً')
                    ->query(fn (Builder $query) => $query->whereNotNull('activated_at')
                        ->where('warranty_expires_at', '>', now())
                        ->where('warranty_expires_at', '<=', now()->addDays(30))),
            ])
            ->actions([
                EditAction::make()
                    ->label('تعديل'),
                Action::make('view_public')
                    ->label('صفحة العميل')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Warranty $record): string => route('warranty.show', $record->public_token))
                    ->openUrlInNewTab(),
                Action::make('download_qr')
                    ->label('تنزيل QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Warranty $record): string => route('warranty.qr.download', $record))
                    ->openUrlInNewTab(),
                Action::make('reset')
                    ->label('إعادة تعيين')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('إعادة تعيين بيانات الجهاز')
                    ->modalDescription('سيتم مسح جميع بيانات الجهاز المرتبطة بهذا الرمز.')
                    ->action(function (Warranty $record) {
                        $record->update([
                            'device_type' => null,
                            'imei' => null,
                            'warranty_expires_at' => null,
                            'activated_at' => null,
                        ]);
                        Log::info('Warranty reset', ['id' => $record->id, 'by' => auth()->id()]);
                        Notification::make()->title('تم إعادة تعيين الضمان')->success()->send();
                    }),
                DeleteAction::make()
                    ->label('حذف')
                    ->visible(fn (Warranty $record): bool => $record->activated_at === null)
                    ->modalHeading('حذف الرمز')
                    ->modalDescription('سيتم حذف هذا الرمز بشكل دائم.'),
                Action::make('print')
                    ->label('طباعة')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Warranty $record): string => route('warranty.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarranties::route('/'),
            'create' => Pages\CreateWarranty::route('/create'),
            'edit' => Pages\EditWarranty::route('/{record}/edit'),
        ];
    }
}
