<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Bill extends Model
{
    use HasFactory;
    static public function getPercentPerCodeDetraction($code)
    {
        $catalog54 = [
            '001'=>10.00,
            '003'=>10.00,
            '004'=>4.00,
            '005'=>4.00,
            '006'=>10.00,
            '007'=>4.00,
            '008'=>10.00,
            '009'=>15.00,
            '010'=>10.00,
            '011'=>4.00,
            '012'=>4.00,
            '013'=>10.00,
            '014'=>10.00,
            '015'=>10.00,
            '016'=>10.00,
            '017'=>10.00,
            '018'=>10.00,
            '019'=>10.00,
            '020'=>4.00,
            '021'=>10.00,
            '022'=>10.00,
            '023'=>1.50,
            '024'=>1.50,
            '025'=>10.00,
            '026'=>10.00,
            '029'=>4.00,
            '030'=>10.00,
            '031'=>10.00,
            '032'=>4.00,
            '033'=>4.00,
            '034'=>10.00,
            '035'=>4.00,
            '036'=>10.00,
            '037'=>12.00,
            '039'=>10.00,
            '040'=>4.00,
        ];
        return $catalog54[$code];
    }
}
