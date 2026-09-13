<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navaznost extends Model
{
    use HasFactory;

    protected $table = 'navaznosti';
    public $timestamps = false;
    protected $guarded = [];
}
