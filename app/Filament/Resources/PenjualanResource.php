<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\Produk; // Tambahkan model Produk
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('pelanggan_id')
                ->label('Pelanggan')
                ->relationship('pelanggan', 'name')
                ->required(),
            Forms\Components\Select::make('produk_id')
                ->label('Produk')
                ->relationship('produk', 'name') 
                ->required(),
            Forms\Components\TextInput::make('jumlah')
                ->label('Jumlah')
                ->required()
                ->numeric()
                ->minValue(1)
                ->afterStateUpdated(function ($state, callable $get) {
                    $produk = Produk::find($get('produk_id'));
                    if ($produk) {
                        return $produk->harga * $state; // Menghitung total harga
                    }
                }),
        ]);
}

public static function afterSave($record): void // Ganti dari afterCreate menjadi afterSave
{
    // Ambil harga produk
    $produk = Produk::find($record->produk_id);
    
    // Hitung total harga
    if ($produk) {
        $record->total_harga = $produk->harga * $record->jumlah;
        $record->saveQuietly(); // Simpan tanpa memicu event lainnya
    }
}



    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.name')->label('Pelanggan'),
                Tables\Columns\TextColumn::make('produk.name')->label('Produk'), // Tambahkan kolom produk
                Tables\Columns\TextColumn::make('jumlah')->label('Jumlah'),
                Tables\Columns\TextColumn::make('total_harga')->label('Total Harga'), // Tambahkan kolom total harga
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualan::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
