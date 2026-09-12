<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mistenka extends Model
{
    use HasFactory;

    protected $table = 'mistenky';
    public $timestamps = false;
    protected $guarded = [];
}
