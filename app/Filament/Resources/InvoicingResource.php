<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoicingResource\Pages;
use App\Filament\Resources\InvoicingResource\RelationManagers;
use App\Models\Invoicing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Seller;
use Filament\Tables\Filters\Filter;

class InvoicingResource extends Resource
{
    protected static ?string $model = Invoicing::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Vendas';

    protected static ?string $navigationLabel = 'Faturamento';

    protected static ?string $modelLabel = 'Faturamento';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('seller_id')
                    ->required()
                    ->placeholder('Selecione um Vendedor')
                    ->label('Vendedor')
                    ->options(Seller::all()->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('initial_date')
                    ->required(),
                Forms\Components\DatePicker::make('final_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('Intervalo de Datas')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Data Inicial')
                            ->placeholder('Selecione a data inicial'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Data Final')
                            ->placeholder('Selecione a data final'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn(Builder $query, $from) => $query->where('initial_date', '>=', $from)
                            )
                            ->when(
                                $data['to_date'],
                                fn(Builder $query, $to) => $query->where('final_date', '<=', $to)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoicings::route('/'),
            'create' => Pages\CreateInvoicing::route('/create'),
            'view' => Pages\ViewInvoicing::route('/{record}'),
            'edit' => Pages\EditInvoicing::route('/{record}/edit'),
        ];
    }
}
