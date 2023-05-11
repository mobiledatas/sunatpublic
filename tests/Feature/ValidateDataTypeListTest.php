<?php

namespace Tests\Feature;

use App\Http\Controllers\SharepointController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ValidateDataTypeListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_types()
    {
        $spc = new SharepointController;        
        $recurrentes = $spc->getRecurrentes();
        foreach ($recurrentes as $r) {
            // echo print_r($r,true);
            echo("Con detraccion: ".$r->getProperty('withDetraction')."\n");
            echo("Tipo: ".$r->getProperty('Type_invoiceOrder')."\n");
        }
        // $response = $this->get('/');

        // $response->assertStatus(200);
    }

    public function test_customer()
    {
        $spc = new SharepointController;        
        $recurrentes = $spc->getRecurrentes();
        foreach ($recurrentes as $r) {
            Log::info(print_r($r,true));
            // Log::info(print_r($r->getProperty('ruc_customer'),true));
            $customer = $spc->getCustomer($r->getProperty('ruc_customer'));
            if($customer[0] != null){
                print_r($customer[0]->getProperty('Bussines_name'));
            }
        }
    }
}
