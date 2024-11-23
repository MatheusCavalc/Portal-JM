<?php

namespace App\Filament\Resources\InvoicingResource\Pages;

use App\Filament\Resources\InvoicingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoicing extends ViewRecord
{
    protected static string $resource = InvoicingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
