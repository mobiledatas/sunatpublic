<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    

    public function invoicelines()
    {
        return $this->hasMany(InvoiceLine::class,'id_invoice','id');
    }
    public function quotes()
    {
        return $this->hasMany(InvoiceQuote::class,'id_invoice','id');
    }

    public function detraction()
    {
        return $this->hasOne(Detraction::class,'id_invoice','id');
    }
}
