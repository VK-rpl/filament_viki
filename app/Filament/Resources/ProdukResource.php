<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProdukExport;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->maxLength(10),
                Forms\Components\TextInput::make('stok')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxLength(10),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Produk'),
                Tables\Columns\TextColumn::make('harga')->label('Harga'),
                Tables\Columns\TextColumn::make('stok')->label('Stok'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportExcel')
                    ->label('Ekspor ke Excel')
                    ->color('success')
                    ->action(function () {
                        return static::exportExcel();
                    }),

                Tables\Actions\Action::make('exportPdf')
                    ->label('Ekspor ke PDF')
                    ->color('success')
                    ->action(function () {
                        return static::exportPdf();
                    }),
            ]);
    }

    public static function exportExcel()
    {
        return Excel::download(new ProdukExport, 'produk.xlsx');
    }

    public static function exportPdf()
{
    $products = Produk::all()->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => self::sanitize($product->name),
            'harga' => self::sanitize($product->harga),
            'stok' => self::sanitize($product->stok),
            'created_at' => $product->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $product->updated_at->format('Y-m-d H:i:s'),
        ];
    });

    $pdf = Pdf::loadView('exports.produk', ['products' => $products]);

    return $pdf->download('produk.pdf');
}


    private static function sanitize($value)
    {
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduk::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
