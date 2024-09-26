<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    // Izinkan atribut ini untuk mass assignment
    protected $fillable = ['name', 'email', 'phone'];
}
