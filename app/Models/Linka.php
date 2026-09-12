<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Composite primary key: (cislo_linky, rozliseni_linky). See Oznacnik for the caveat. */
class Linka extends Model
{
    use HasFactory;

    protected $table = 'linky';
    protected $primaryKey = 'cislo_linky';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'objizdkovy_jr' => 'boolean',
        'seskupeni_spoju' => 'boolean',
        'pouziti_oznacniku' => 'boolean',
    ];
}
