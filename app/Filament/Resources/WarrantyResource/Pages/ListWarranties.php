<?php

namespace App\Filament\Resources\WarrantyResource\Pages;

use App\Filament\Resources\WarrantyResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarranties extends ListRecords
{
    protected static string $resource = WarrantyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('توليد رموز QR')
                ->icon('heroicon-o-qr-code')
                ->url(route('filament.admin.pages.generate-qr-codes'))
                ->color('primary'),
            Action::make('scan')
                ->label('مسح QR Code')
                ->icon('heroicon-o-camera')
                ->url(route('filament.admin.pages.scan-warranty-qr'))
                ->color('success'),
            CreateAction::make()
                ->label('إضافة ضمان جديد'),
        ];
    }
}
