<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zastavka extends Model
{
    use HasFactory;

    protected $table = 'zastavky';
    protected $primaryKey = 'cislo_zastavky';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];
}
