<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Office365\Runtime\Auth\UserCredentials;
use Office365\SharePoint\CamlQuery;
use Office365\SharePoint\ClientContext;
use Office365\SharePoint\ListItemCollection;

class SharepointController extends Controller
{
    private UserCredentials $creds;
    private ClientContext $client;
    //
    function __construct()
    {
        $this->creds  = new UserCredentials(env('SP_USER'),env('SP_USER_PASSWORD'));
        $this->client = new ClientContext(env('SP_SITE'));
    }

    public function getRecurrentes():ListItemCollection | null
    {
        
        try {
            
            $client = ($this->client)->withCredentials($this->creds);
            $web = $client->getWeb();
            $list = $web->getLists()->getByTitle("Recurrentes");
            $items = $list->getItems();
            $client->load($items);
            $client->executeQuery();
            return ($items);        
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return null;
        }
        
    }

    public function getItems(int $id)
    {
        try {
            // print("Recurrent ID {$id} \n");
            $client = ($this->client)->withCredentials($this->creds);
            $web = $client->getWeb();
            $list = $web->getLists()->getByTitle("Recurrente_items");
            
            $items = $list->getItems()->filter("ID_data_invoice eq {$id}");
        
            $client->load($items);
            $client->executeQuery();
            return ($items);        
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return null;
        }
    }

    public function getCustomer($ruc)
    {
        try {
            $client = (new ClientContext(env('SP_SITE_COBRANZAS')))->withCredentials($this->creds);
            $web = $client->getWeb();
            $list = $web->getLists()->getByTitle("Customer");
            $items = $list->getItems()->filter("RUC_customer eq {$ruc}");
            $client->load($items);
            $client->executeQuery();
            return ($items);        
            //code...
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return null;
        }


    }
}
