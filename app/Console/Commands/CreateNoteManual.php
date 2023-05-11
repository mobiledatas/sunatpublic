<?php

namespace App\Console\Commands;

use App\Http\Controllers\ApiSunatController;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\Document;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Console\Command;
use Luecano\NumeroALetras\NumeroALetras;

class CreateNoteManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:note';

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

        $customer = (new Client())
            ->setTipoDoc('6') //Ruc
            ->setNumDoc('20607169714')
            ->setTelephone('')
            ->setRznSocial('CONSORCIO PMC TALARA');

        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('SAN BORJA')
            ->setUrbanizacion('-')//MDS
            ->setDireccion('Av. San Luis 1502-San Borja')
            ->setCodLocal('0000');  // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc('20607169714')
            ->setTelephone('+51 17390932')
            ->setNombreComercial('MOBILE DATASOLUTIONS SAC')
            ->setRazonSocial('MOBILE DATASOLUTIONS SAC')
            ->setAddress($address);

        $note = new Note();
        //Log::info($request->input('invoice.serie') . '-' . $request->input('invoice.correlative'));
        $note

            ->setUblVersion('2.1')
            ->setTipoDoc('07')
            ->setSerie('FF01')
            ->setCorrelativo('39')
            ->setFechaEmision((new DateTime()))
            ->setTipDocAfectado('01') // Tipo Doc: Factura
            ->setNumDocfectado('F001-85') // Factura: Serie-Correlativo
            ->setCodMotivo('09')// Catalogo. 09
            ->setDesMotivo('ANULACION DE OPERACION')
            ->setTipoMoneda('USD')
            ->setCompany($company)
            ->setClient($customer)
            ->setMtoOperGravadas(16788.58)//MONTO DE FACTURA OPGRAVADA COMO NUMERO
            ->setMtoIGV(3021.94)//IGV
            ->setTotalImpuestos(3021.94)//IGV
            //->setMtoImpVenta(16788.58);//MONTO VNETA
            ->setMtoImpVenta(19810.52);//MONTO VNETA
        $base = 0;


       //dretraccion no

        //forma de pago solucion pago al contado ######
        //$note->setFormaPago(new FormaPagoContado());

        $note->setFormaPago((new FormaPagoCredito(19810.52)));
        $quotes = [(new Cuota)
        ->setMoneda('USD')
        ->setMonto(19810.52)
        ->setFechaPago(new DateTime('17-03-2023'))];
        $note->setCuotas($quotes);


        $lines = [
            (new SaleDetail)
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(12)
            ->setMtoValorUnitario(68.77)
            ->setDescripcion('Mail Cloud - Microsoft Office 365 Business Basic (Febrero 2023 Febrero 2024)')
            ->setMtoBaseIgv(12 * 68.77) //cantidad * precio unitario
            ->setIcbper(0.00)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(round((12 * 68.77) * 0.18, 2))
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(round((12 * 68.77) * 0.18, 2)) // Suma de impuestos en el detalle
            ->setMtoValorVenta(825.24)
            ->setMtoPrecioUnitario(round(68.77 * 1.18, 2)),
            (new SaleDetail)
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(100)
            ->setMtoValorUnitario(155.52)
            ->setDescripcion('Mail Cloud - Microsoft Office 365 Business Standard (Febrero 2023 Febrero 2024)')
            ->setMtoBaseIgv(155.52*100) //cantidad * precio unitario
            ->setIcbper(0.00)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(round((155.52*100) * 0.18, 2))
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(round((155.52*100) * 0.18, 2)) // Suma de impuestos en el detalle
            ->setMtoValorVenta(15552)
            ->setMtoPrecioUnitario(round(155.52 * 1.18, 2)),
            (new SaleDetail)
            ->setUnidad('NIU') // Unidad - Catalog. 03
            ->setCantidad(2)
            ->setMtoValorUnitario(257.22)
            ->setDescripcion('Mail Cloud - Office 365 E3 - (Febrero 2023 Febrero 2024)')
            ->setMtoBaseIgv(257.22*2) //cantidad * precio unitario
            ->setIcbper(0.00)
            ->setPorcentajeIgv(18.00) // 18%
            ->setIgv(round((257.22*2) * 0.18, 2))
            ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            ->setTotalImpuestos(round((257.22*2) * 0.18, 2)) // Suma de impuestos en el detalle
            ->setMtoValorVenta(514.44)
            ->setMtoPrecioUnitario(round(257.22 * 1.18, 2))
        ];

        //$count = 16891.68;
        $count=16788.58;
        //$count=$count-103.10;
        //$count=$count-$descuento;
        $note->setMtoOperGravadas($count)
            ->setMtoIGV(round(3021.94,2))
            ->setTotalImpuestos(round(3021.94, 2))
            ->setMtoImpVenta(round(19810.52, 2))
            //agregado recientemente
            ->setTipoMoneda('USD');

        $note->setLegends([
            (new Legend)->setCode('1000')
                ->setValue('SON DIECINUEVE MIL OCHOCIENTOS DIEZ CON 52/100 DOLARES')
        ]);

        $note->setDetails($lines);

        $result=(new ApiSunatController)->sendDoc($note);

        if (!$result['status']) {
            $this->info('nota generada');
            (new ApiSunatController)->generateReport($note);
        }else{
            Log::info("error de sunat");
            Log::info(json_encode($result['message']));
            //Log::info(json_encode(sendDoc));
        }

    }
}
