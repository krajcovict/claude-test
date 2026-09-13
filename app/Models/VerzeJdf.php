<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerzeJdf extends Model
{
    use HasFactory;

    protected $table = 'verze_jdf';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'datum_vyroby_davky' => 'date',
    ];
}
