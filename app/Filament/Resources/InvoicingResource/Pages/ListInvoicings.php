<?php

namespace App\Filament\Resources\InvoicingResource\Pages;

use App\Filament\Resources\InvoicingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoicings extends ListRecords
{
    protected static string $resource = InvoicingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
