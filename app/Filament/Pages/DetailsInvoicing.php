<?php

namespace App\Filament\Pages;

use App\Models\Invoicing;
use Filament\Pages\Page;

class DetailsInvoicing extends Page
{
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Vendas';

    protected static ?string $navigationLabel = 'Detalhes Faturamento';

    protected static ?string $modelLabel = 'Detalhes Faturamento';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.details-invoicing';
}
