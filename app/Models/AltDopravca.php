<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AltDopravca extends Model
{
    use HasFactory;

    protected $table = 'altdop';
    public $timestamps = false;
    protected $guarded = [];
}
