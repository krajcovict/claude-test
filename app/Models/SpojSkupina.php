<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpojSkupina extends Model
{
    use HasFactory;

    protected $table = 'spoj_skup';
    protected $primaryKey = 'kod_skupiny_spoju';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];
}
