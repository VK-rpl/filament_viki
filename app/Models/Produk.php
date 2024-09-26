<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = ['name', 'harga', 'stok']; // Tambahkan 'stok' ke fillable
}
