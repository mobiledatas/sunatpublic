<?php

namespace Tests\Feature;

use App\Http\Controllers\SharepointController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Office365\Runtime\Auth\UserCredentials;
use Office365\SharePoint\ClientContext;
use Office365\SharePoint\ListItemCollection;
use Tests\TestCase;
use Thybag\SharePointAPI;

class GetSharepointListTest extends TestCase
{
    // protected $baseUrl = 'http://127.0.0.1:8000';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_order_list()
    {
        $creds  = new UserCredentials('flow@mobiledatas.com','Pun70157');
        $client = (new ClientContext("https://mdsolutions468.sharepoint.com/sites/Appfacturasrecurrentesdev"))->withCredentials($creds);
        $web = $client->getWeb();
        $list = $web->getLists()->getByTitle("Recurrentes");
        $items = $list->getItems();
        $client->load($items);
        $client->executeQuery();
        foreach($items as $recurrente){
            print_r("-----------------------------\n");
            print("Customer {$recurrente->getProperty('Bussines_name_customer')}\n");
            $children = (new SharepointController)->getItems($recurrente->getProperty('ID'));
            foreach ($children as $child) {
                print_r("{$child->getProperty('Detail')} {$child->getProperty('ID_data_invoice')}\n");
            }
        }
        
        // $this->baseUrl = "http://127.0.0.1:8000";
        // require_once app_path().'./../vendor/thybag/php-sharepoint-lists-api/SharePointAPI.php';
        // $sp = new SharePointAPI('flow@mobiledatas.com','Pun70157','https://mdsolutions468.sharepoint.com/sites/Appfacturasrecurrentesdev/_vti_bin/Lists.asmx?WSDL','SPONLINE');
        
        // $sp->getLists();
        // Log::info(print_r($sp,true));
        // Log::info(print_r($recurrentes,true));

        // $response = $this->get('/');

        // $response->assertStatus(200);
    }
}
