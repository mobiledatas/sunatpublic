<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrelativeService extends Model
{
    use HasFactory;

    static public function deleteAll()
    {
        self::truncate();
    }
}
