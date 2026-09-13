<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Composite primary key: (cislo_linky, rozliseni_linky, cislo_spoje, cislo_tarifni). */
class ZasSpoj extends Model
{
    use HasFactory;

    protected $table = 'zasspoje';
    protected $primaryKey = 'cislo_linky';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];
}
