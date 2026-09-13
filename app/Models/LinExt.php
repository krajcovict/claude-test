<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Composite primary key: (cislo_linky, rozliseni_linky, poradi). */
class LinExt extends Model
{
    use HasFactory;

    protected $table = 'lin_ext';
    protected $primaryKey = 'cislo_linky';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];
}
