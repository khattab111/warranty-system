<?php

namespace App\Filament\Resources\WarrantyResource\Pages;

use App\Filament\Resources\WarrantyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWarranty extends CreateRecord
{
    protected static string $resource = WarrantyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['public_token'] = (string) \Illuminate\Support\Str::uuid();
        return $data;
    }
}
