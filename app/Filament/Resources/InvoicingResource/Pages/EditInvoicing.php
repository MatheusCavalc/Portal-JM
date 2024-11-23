<?php

namespace App\Filament\Resources\InvoicingResource\Pages;

use App\Filament\Resources\InvoicingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoicing extends EditRecord
{
    protected static string $resource = InvoicingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
