<?php

namespace App\Console\Commands;

use DateTime;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GetCdrComunicadoDeBaja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getcdr:comunicadobaja';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $this->info(json_encode(new DateTime('14-01-2023'),JSON_PRETTY_PRINT));
        
        $see = new See();
        $see->setCertificate(file_get_contents(storage_path('certificate/') . 'certificado_mds.pem'));
        $see->setService(SunatEndpoints::FE_PRODUCCION);
        // $this->see->setClaveSOL($this->ruc,$this->user,$this->pass);
        $see->setClaveSOL('20607169714', 'MDURANDF', 'M4rioDurand');

        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('SAN BORJA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. San Luis 1502 - San Borja')
            ->setCodLocal('0000');  // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc('20607169714')
            ->setTelephone('+51 017390932')
            ->setNombreComercial('MOBILE DATA SOLUTIONS SAC')
            ->setRazonSocial('MOBILE DATA SOLUTIONS SAC')
            ->setAddress($address);
        $detail1 = new VoidedDetail();
        $detail1
            //01:factura, 07:notacredito
            ->setTipoDoc('07')
            ->setSerie('FF01')
            ->setCorrelativo('30')
            ->setDesMotivoBaja('NOTA DE CRÉDITO EMITIDA ERRONEAMENTE');

        $voided = new Voided();
        $voided->setCorrelativo('5')
            // Fecha Generacion menor que Fecha comunicacion
            ->setFecGeneracion((new DateTime('14-01-2023'))->modify('+1 day'))
            ->setFecComunicacion(new DateTime())
            ->setCompany($company)
            ->setDetails([$detail1]);

        $res = $see->getStatus('202310851418840');
        if ($res->isSuccess()) {
            // $cdr = $res->getCdrResponse();
            // $this->info($res->getCdrZip());
            File::put(storage_path('app/cdr/') . $voided->getName() . '.zip', $res->getCdrZip());

            $this->info("Se obtuvo el cdr de respuesta");
            
        } else {
            $this->info("Ticket: 202310851418840");
            $this->info("Code: " . json_encode($res->getError()->getCode()));
            $this->info("Message: " . json_encode($res->getError()->getMessage()));
        }
    }
}
