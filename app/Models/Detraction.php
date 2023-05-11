<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detraction extends Model
{
    use HasFactory;

    public function invoice()
    {
        $this->belongsTo(Invoice::class,'id_invoice','invoices.id');
    }
}
