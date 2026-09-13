<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PevnyKod extends Model
{
    use HasFactory;

    protected $table = 'pevnykod';
    protected $primaryKey = 'cislo_pevneho_kodu';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
