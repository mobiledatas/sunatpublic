<?php

namespace Tests\Feature;

use App\Http\Controllers\SharepointController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GenerateBillsOfRecurrentesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_generate_bills_of_list_sharepoint()
    {
        Log::info("test_store_cred_PEN");
        Artisan::call('migrate');
        Artisan::call('db:seed');
        $user = User::all()->first();
        
        $requestLogin = $this->withHeaders([
            'Content-Type'=>'application/json'
        ])->postJson('/api/v1/token',[
            'email'=>$user->email,
            'password'=>'123mdsclient'
        ]);
        $spc = new SharepointController;
        $recurrentes = $spc->getRecurrentes();
        foreach ($recurrentes as $r) {
            $linesBill = $spc->getItems($r->getProperty('ID'));

            $lineArray = [];

            foreach ($linesBill as $line) {
                
                
            }
        
        }

        

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
