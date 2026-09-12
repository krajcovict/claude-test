<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Composite primary key: (ic, rozliseni_dopravce). See Oznacnik for the caveat. */
class Dopravca extends Model
{
    use HasFactory;

    protected $table = 'dopravci';
    protected $primaryKey = 'ic';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
