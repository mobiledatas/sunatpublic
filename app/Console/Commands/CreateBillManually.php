<?php

namespace App\Console\Commands;

use App\Http\Controllers\ApiSunatController;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Console\Command;
use Luecano\NumeroALetras\NumeroALetras;

class CreateBillManually extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:bill';

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
        $invoice = new Invoice();

        //DATOS DEL CLIENTE
        $customer = new Client();
        $clientAddress = new Address();
        $clientAddress ->setDepartamento('')
        ->setProvincia('')
        ->setDistrito('')
        ->setUrbanizacion('-')
        ->setDireccion('AV. JOSE LARCO 1301 URB. CERCADO DE MIRAFLORES LIMA LIMA MIRAFLORES');
        $customer->setTipoDoc('6') //Ruc
        ->setNumDoc('20419020606')
        ->setTelephone('')
        ->setRznSocial('MIRANDA & AMADO ABOGADOS S. CIVIL DE R.L.')
        ->setAddress($clientAddress);

        //DATOS DE LA EMPRESA
        $address = new Address();
        $address->setUbigueo('150101')
        ->setDepartamento('LIMA')
        ->setProvincia('LIMA')
        ->setDistrito('SAN BORJA')
        ->setUrbanizacion('-')
        ->setDireccion('Av. San Luis 1502-San Borja')
        ->setCodLocal('0000');

        $company = new Company();
        $company->setRuc('20607169714')
        ->setTelephone('+51 17390932')
        ->setNombreComercial('MOBILE DATA SOLUTIONS SAC')
        ->setRazonSocial('MOBILE DATA SOLUTIONS SAC')
        ->setAddress($address);

        $item = new SaleDetail();
        $item->setCodProducto('P001')
        ->setDescripcion("SERVICIO MICROSOFT AZURE - Cloud - ABRIL 2023")
        ->setUnidad('NIU') // Unidad - Catalog. 03
        ->setCantidad(1)
        ->setMtoValorUnitario(844.09) //LineExtensionAmount
        ->setMtoValorVenta(844.09) //Price [PriceAmount]
        ->setMtoBaseIgv(844.09) //TaxableAmount []cantidad * precio unitario --
        ->setIcbper(0.00)
        ->setPorcentajeIgv(18) //TaxAmount 18%
        ->setIgv(151.94) //TaxAmount
        ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
        ->setTotalImpuestos(151.94) //TaxAmount [] Suma de impuestos en el detalle
        ->setMtoPrecioUnitario(996.03); //PriceAmount TOTAL

        $legend1 = new Legend();
        $legend1->setCode('2006')
        ->setValue('-');
        $legend2 = new Legend();
        $legend2->setCode('1000')->setValue((new NumeroALetras())->toInvoice(996.03, 2, $invoice->getTipoMoneda() == 'PEN' ? 'soles' : 'dolares'));

        $quote = new Cuota();
        $quote->setMonto(996.03)
        ->setMoneda('USD')
        ->setFechaPago((new DateTime('2023-05-31'))->modify('+1 day'));

        $invoice
                ->setUblVersion('2.1')
                ->setTipoOperacion('0101') // Venta - Catalog. 51
                ->setTipoDoc('01') // Factura - Catalog. 01
                ->setSerie('F001')
                //->setCorrelativo('23')
                ->setCorrelativo('150')
                ->setFechaEmision((new DateTime('2023-05-09 13:05:00-05:00'))) //->modify('+1 day') Zona horaria: Lima
                ->setFormaPago((new FormaPagoCredito(996.03))) // FormaPago: Contado
                ->setTipoMoneda('USD') // Sol - Catalog. 02
                ->setCompany($company)
                ->setClient($customer)
                ->setMtoOperGravadas(844.09)
                ->setMtoIGV(151.94)
                ->setTotalImpuestos(151.94)
                ->setValorVenta(844.09)
                ->setSubTotal(996.03)
                ->setMtoImpVenta(996.03)
                ->setDetails([$item])
                ->setLegends([$legend1,$legend2])
                ->setCuotas([$quote]);

        (new ApiSunatController)->generateReport($invoice);
        // return Command::SUCCESS;
    }
}
