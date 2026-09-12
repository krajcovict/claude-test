<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Composite primary key: (cislo_zastavky, kod_oznacniku).
 * Eloquent has no native composite-key support, so only the first
 * column is registered as the nominal primary key below. find()/save()
 * by key won't be reliable for this model — insert via the factory or
 * the query builder using both columns explicitly.
 */
class Oznacnik extends Model
{
    use HasFactory;

    protected $table = 'oznacniky';
    protected $primaryKey = 'cislo_zastavky';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];
}
