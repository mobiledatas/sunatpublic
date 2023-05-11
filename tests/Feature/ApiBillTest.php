<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ApiBillTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store_cred_usd()
    {
        Log::info("test_store_cred_USD");
        Artisan::call('migrate');
        Artisan::call('db:seed');
        $user = User::all()->first();
        
        $requestLogin = $this->withHeaders([
            'Content-Type'=>'application/json'
        ])->postJson('/api/v1/token',[
            'email'=>$user->email,
            'password'=>'123mdsclient'
        ]);
        // Log::info(json_encode($requestLogin,JSON_PRETTY_PRINT));

        // Log::info($user);
        $payload = array (
            'company' => 
            array (
              'name' => 'MOBILE DATA SOLUTIONS S.A.C.',
              'ruc' => '20123456789',
              'phone' => '+5197481095',
              'address' => 
              array (
                'department' => 'LIMA',
                'province' => 'LIMA',
                'district' => 'LIMA',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'customer' => 
            array (
              'name' => 'DANIEL CÓRDOVA',
              'ruc' => '10753674654',
              'phone' => '',
              'address' => 
              array (
                'department' => '',
                'province' => '',
                'district' => '',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'invoice' => 
            array (
              'date_emission' => '29-09-2022',
              'currency' => 'USD',
              'payment_method' => 'CRED',
              'advanced' => '10.00',
              'discount' => '8.00',
              'isc' => '0.00',
              'other_charges' => '0.00',
              'other_tributes' => '0.00',
              'items' => 
              array (
                0 => 
                array (
                  'quantity' => '1',
                  'measurement_unit' => 'NIU',
                  'description' => 'Servicio de hosting 1',
                  'unit_value' => '50.00',
                  'icbper' => '0.00',
                ),
                1 => 
                array (
                  'quantity' => '1',
                  'measurement_unit' => 'NIU',
                  'description' => 'Servicio de hosting 2',
                  'unit_value' => '50.00',
                  'icbper' => '0.00',
                ),
              ),
              'quotes' => 
              array (
                0 => 
                array (
                  'amount' => '88.7',
                  'currency' => 'USD',
                  'payment_date' => '01-10-2022',
                ),
              ),
              'legends' => 
              array (
                0 => 
                array (
                  'code' => '2006',
                  'value' => 'Operacion sujeta a detraccion',
                ),
              ),
              'detraction' => 
              array (
                'cod_bien_detraction' => '019',
                'cod_medio_pago' => '001',
                'bank_account' => '0004-3342343243',
              ),
            ),
        );
        $response = $this->withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$requestLogin['token']
        ])->postJson('api/v1/bill',$payload);
        Log::info(json_encode($response,JSON_PRETTY_PRINT));
        // Log::info(json_encode($response));
        
        $response->assertStatus(200);
    }
    public function test_store_cont_usd()
    {
        Log::info("test_store_cred_USD");
        Artisan::call('migrate');
        Artisan::call('db:seed');
        $user = User::all()->first();
        
        $requestLogin = $this->withHeaders([
            'Content-Type'=>'application/json'
        ])->postJson('/api/v1/token',[
            'email'=>$user->email,
            'password'=>'123mdsclient'
        ]);
        // Log::info(json_encode($requestLogin,JSON_PRETTY_PRINT));

        // Log::info($user);
        $payload = array (
            'company' => 
            array (
              'name' => 'MOBILE DATA SOLUTIONS S.A.C.',
              'ruc' => '20123456789',
              'phone' => '+5197481095',
              'address' => 
              array (
                'department' => 'LIMA',
                'province' => 'LIMA',
                'district' => 'LIMA',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'customer' => 
            array (
              'name' => 'DANIEL CÓRDOVA',
              'ruc' => '10753674654',
              'phone' => '+51974781095',
              'address' => 
              array (
                'department' => 'LIMA',
                'province' => 'LIMA',
                'district' => 'LIMA',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'invoice' => 
            array (
              'date_emission' => '29-09-2022',
              'currency' => 'USD',
              'payment_method' => 'CONT',
              'advanced' => '6.20',
              'discount' => '0.00',
              'isc' => '0.00',
              'other_charges' => '0.00',
              'other_tributes' => '0.00',
              'items' => 
              array (
                0 => 
                array (
                  'quantity' => '1',
                  'measurement_unit' => 'NIU',
                  'description' => 'Servicio de hosting 2',
                  'unit_value' => '100.00',
                  'icbper' => '0.00',
                ),
              ),
              
              'legends' => 
              array (
                0 => 
                array (
                  'code' => '2006',
                  'value' => 'Operacion sujeta a detraccion',
                ),
              ),
              'detraction' => 
              array (
                'cod_bien_detraction' => '014',
                'cod_medio_pago' => '001',
                'bank_account' => '0004-3342343243',
              ),
            ),
        );
        $response = $this->withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$requestLogin['token']
        ])->postJson('api/v1/bill',$payload);
        // Log::info(json_encode($response,JSON_PRETTY_PRINT));
        // Log::info(json_encode($response));
        
        $response->assertStatus(200);
    }

    public function test_store_cred_pen()
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
        // Log::info(json_encode($requestLogin,JSON_PRETTY_PRINT));

        // Log::info($user);
        $payload = array (
            'company' => 
            array (
              'name' => 'MOBILE DATA SOLUTIONS S.A.C.',
              'ruc' => '20123456789',
              'phone' => '+5197481095',
              'address' => 
              array (
                'department' => 'LIMA',
                'province' => 'LIMA',
                'district' => 'LIMA',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'customer' => 
            array (
              'name' => 'DANIEL CÓRDOVA',
              'ruc' => '10753674654',
              'phone' => '+51974781095',
              'address' => 
              array (
                'department' => 'LIMA',
                'province' => 'LIMA',
                'district' => 'LIMA',
                'urbanization' => '-',
                'address' => 'Avenida',
              ),
            ),
            'invoice' => 
            array (
              'date_emission' => '29-09-2022',
              'currency' => 'PEN',
              'payment_method' => 'CRED',
              'advanced' => '6.20',
              'discount' => '0.00',
              'isc' => '0.00',
              'other_charges' => '0.00',
              'other_tributes' => '0.00',
              'items' => 
              array (
                0 => 
                array (
                  'quantity' => '1',
                  'measurement_unit' => 'NIU',
                  'description' => 'Servicio de hosting 3',
                  'unit_value' => '100.00',
                  'icbper' => '0.00',
                ),
              ),
              'quotes' => 
              array (
                0 => 
                array (
                  'amount' => '100.62',
                  'currency' => 'PEN',
                  'payment_date' => '01-10-2022',
                ),
              ),
              'legends' => 
              array (
                
                0 => 
                array (
                  'code' => '2006',
                  'value' => 'Operacion sujeta a detraccion',
                ),
              ),
              'detraction' => 
              array (
                'cod_bien_detraction' => '014',
                'cod_medio_pago' => '001',
                'bank_account' => '0004-3342343243',
              ),
            ),
        );
        $response = $this->withHeaders([
            'Content-Type'=>'application/json',
            'Authorization'=>'Bearer '.$requestLogin['token']
        ])->postJson('api/v1/bill',$payload);
        Log::info(json_encode($response,JSON_PRETTY_PRINT));
        // Log::info(json_encode($response));
        
        $response->assertStatus(200);
    }
}
