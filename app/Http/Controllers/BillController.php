<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Mail\MailCobranzas;
use App\Models\Bill;
use App\Models\CorrelativeService;
use App\Models\Detraction as ModelsDetraction;
use App\Models\Invoice as ModelsInvoice;
use App\Models\InvoiceLine;
use App\Models\InvoiceQuote;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\Detraction;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Prepayment;
use Greenter\Model\Sale\SaleDetail;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Luecano\NumeroALetras\NumeroALetras;

use function PHPUnit\Framework\isEmpty;

// use Illuminate\Support\Facades\Storage;


class BillController extends Controller
{
    private ApiSunatController $api;

    function __construct()
    {
        $this->api = new ApiSunatController();
    }
    //
    public function index(Request $request)
    {
    }

    public function show(Request $request, $bill)
    {
        try {
            $invoice = ModelsInvoice::with(['quotes', 'detraction', 'invoicelines'])->get()->find($bill);
            return  response()->json([
                'invoice' => $invoice,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 403);
        }
    }

    //store -> info que se le envia a sunat- por eso se suma 1 dia
    public function store(BillRequest $request)
    {
        Log::info(json_encode($request->input(),JSON_PRETTY_PRINT));
        // return $this->api->getExchangeRateValue(new DateTime($request->input('invoice.date_emission')));
        $items = $request->input('invoice.items');
        $legends = $request->input('invoice.legends');
        $discount = $request->input('invoice.discount');
        $advanced = $request->input('invoice.advanced');
        $isc = $request->input('invoice.isc');
        $other_charges = $request->input('invoice.other_charges');
        $sumatoriaValoresItems = 0;
        $invoiceItems = [];
        $icbper = 0;
        foreach ($items as $item) {

            $totalItem = ($item['unit_value']) * ($item['quantity']);
            $igv = ($totalItem) * 0.18;
            $sumatoriaValoresItems = $sumatoriaValoresItems + $totalItem;
            $saleDetail = (new SaleDetail())
                // ->setCodProducto('P001')
                ->setUnidad($item['measurement_unit']) // Unidad - Catalog. 03
                ->setCantidad($item['quantity'])
                ->setMtoValorUnitario($item['unit_value'])
                ->setDescripcion($item['description'])
                ->setMtoBaseIgv($item['unit_value'] * $item['quantity']) //cantidad * precio unitario
                ->setIcbper($item['icbper'])
                ->setPorcentajeIgv(18.00) // 18%
                ->setIgv($igv)
                ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos($igv) // Suma de impuestos en el detalle
                ->setMtoValorVenta($item['unit_value'] * $item['quantity'])
                ->setMtoPrecioUnitario(round($item['unit_value'] * 1.18, 2));
            // ;
            array_push($invoiceItems, $saleDetail);
            $icbper = $icbper + $item['icbper'];
            // Log::info('--------------LINE-----------------');
            // Log::info('Linea: ' . $item['description']);
            // Log::info('Precio unitario sin igv: ' . $saleDetail->getMtoValorUnitario());
            // Log::info('Cantidad: ' . $saleDetail->getCantidad());
            // Log::info('Monto base para el igv: ' . $saleDetail->getMtoBaseIgv());
            // Log::info('IGV: ' . $saleDetail->getIgv());
            // Log::info('Precio unitario con igv: ' . $saleDetail->getMtoPrecioUnitario());
        }
        $clientAddress = (new Address)
            ->setDepartamento($request->input('customer.address.department'))
            ->setProvincia($request->input('customer.address.province'))
            ->setDistrito($request->input('customer.address.district'))
            ->setUrbanizacion($request->input('customer.address.urbanization'))
            ->setDireccion($request->input('customer.address.address'));

        $client = (new Client())
            ->setTipoDoc('6') //Ruc
            ->setNumDoc($request->input('customer.ruc'))
            ->setTelephone($request->input('customer.phone'))
            ->setRznSocial($request->input('customer.name'))
            ->setAddress($clientAddress);

        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento($request->input('company.address.department'))
            ->setProvincia($request->input('company.address.province'))
            ->setDistrito($request->input('company.address.district'))
            ->setUrbanizacion($request->input('company.address.urbanization'))
            ->setDireccion($request->input('company.address.address'))
            ->setCodLocal('0000');  // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc($request->input('company.ruc'))
            ->setTelephone($request->input('company.phone'))
            ->setNombreComercial($request->input('company.name'))
            ->setRazonSocial($request->input('company.name'))
            ->setAddress($address);


        //    Log::info(json_encode($correlative));
        //    Log::info($nextCorrelative);
        //     die();

        $arrayCorrelative = $this->getCorrelative();
        $nextCorrelative = $arrayCorrelative['nextCorrelative'];
        $correlative = $arrayCorrelative['correlativeModel'];
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion($request->has('invoice.detraction') && $request->input('invoice.detraction') != null ? '1001' : '0101') // Venta - Catalog. 51
            ->setTipoDoc('01') // Factura - Catalog. 01
            ->setSerie('F001')
            ->setCorrelativo($nextCorrelative)
            ->setFechaEmision((new DateTime($request->input('invoice.date_emission')))->modify('+1 day')) // Zona horaria: Lima

            ->setTipoMoneda($request->input('invoice.currency')) // Sol - Catalog. 02
            ->setCompany($company)
            ->setClient($client)
            ->setDescuentos([]);

        if ($discount > 0.00) {
            $invoice->setDescuentos([
                (new Charge)
                    ->setCodTipo('02')
                    ->setMontoBase($discount)
                    ->setFactor(1)
                    ->setMonto($discount)
            ]);
            $invoice->setMtoDescuentos($discount);
        }

        if ($other_charges > 0.00) {
            $invoice->setCargos([
                (new Charge)
                    ->setCodTipo('47')
                    ->setMontoBase($other_charges)
                    ->setFactor(1)
                    ->setMonto($other_charges)
            ]);
        }

        if ($advanced > 0.00) {
            $invoice->setDescuentos([
                ...$invoice->getDescuentos(),
                (new Charge)
                    ->setCodTipo('04')
                    ->setFactor(1)
                    ->setMonto($advanced)
                    ->setMontoBase($advanced)
            ])
                ->setAnticipos([
                    (new Prepayment)
                        ->setTipoDocRel('02')
                        ->setNroDocRel($invoice->getSerie() . '-' . $invoice->getCorrelativo())
                        ->setTotal($advanced)
                ])
                ->setTotalAnticipos($advanced);
        }


        $igvValorVenta = $sumatoriaValoresItems * 0.18; //OK
        $subtotal = round(($sumatoriaValoresItems - $discount) * 1.18,2); //OK
        $mtoOperGravadas = ($sumatoriaValoresItems - ($discount + $advanced)); //OK
        $importeTotalVenta = round($subtotal - $advanced,2);
        /*
        *
        */


        /***
         *
         */


        $invoice
            ->setMtoOperGravadas(round($mtoOperGravadas, 2))
            ->setMtoIGV(round($mtoOperGravadas * 0.18, 2))
            ->setValorVenta(round($sumatoriaValoresItems - $discount, 2))
            ->setTotalImpuestos(round(($mtoOperGravadas * 0.18) + $isc + $other_charges, 2))
            ->setSubTotal(round(($sumatoriaValoresItems - $discount) * 1.18, 2))
            ->setMtoImpVenta(round($importeTotalVenta, 2));
        // Log::info('Valor de venta: ' . $valorVenta);
        $base = 0;

        if ($request->has('invoice.detraction') && $request->input('invoice.detraction') != null) {
            $base = round(($importeTotalVenta) * Bill::getPercentPerCodeDetraction($request->input('invoice.detraction.cod_bien_detraction')) / 100, 2);


            // Log::info(Bill::getPercentPerCodeDetraction($request->input('invoice.detraction.cod_bien_detraction')) . " %");

            // Log::info('Porcentaje de detraccion resultado (base): '.$invoice->getTipoMoneda().' '.$base);
            // $detract = $base  * ($invoice->getTipoMoneda() == 'USD' ? 3.97:1);
            // $detract = $base;
            $amountDetract = $base;
            if ($invoice->getTipoMoneda() == 'USD') {
                $exchangerate = $this->api->getExchangeRateValue($invoice->getFechaEmision())['venta'];
                $amountDetract = round($exchangerate * floatval($amountDetract), 2);
            }
            $invoice->setDetraccion(
                (new Detraction)
                    ->setCodBienDetraccion($request->input('invoice.detraction.cod_bien_detraction'))
                    ->setCodMedioPago($request->input('invoice.detraction.cod_medio_pago'))
                    ->setCtaBanco($request->input('invoice.detraction.bank_account'))
                    ->setPercent(Bill::getPercentPerCodeDetraction($request->input('invoice.detraction.cod_bien_detraction')))
                    ->setMount($amountDetract)
            );
            Log::info('Monto dolares de detraccion ' . $invoice->getTipoMoneda() . ' ' . $base);
            Log::info('Monto soles de detraccion PEN ' . $amountDetract);
        }


        if ($request->input('invoice.payment_method') == 'CRED') {

            Log::info("Monto pendiente de pago: " . ($invoice->getMtoImpVenta() - $base));

            $invoice->setFormaPago((new FormaPagoCredito($invoice->getMtoImpVenta() - $base)));
            $quotes = $request->input('invoice.quotes');
            $pushQuo = [];
            foreach ($quotes as $q) {
                $quote = (new Cuota())
                    ->setMonto($q['amount'])
                    ->setMoneda($q['currency'])
                    ->setFechaPago((new DateTime($q['payment_date']))->modify('+1 day'));
                // Log::info(json_encode($quote->getFechaPago()));
                array_push($pushQuo, $quote);
            }
            $invoice->setCuotas($pushQuo);
        }

        if ($request->input('invoice.payment_method') == 'CONT') {
            $invoice->setFormaPago((new FormaPagoContado()));
        }

        $invoiceLegends = [
            (new Legend())
                ->setCode('1000')
                //->setValue('SON ' . (new NumeroALetras())->toInvoice(($importeTotalVenta - $base), 2, $invoice->getTipoMoneda() == 'PEN' ? 'soles' : 'dolares'))
                ->setValue('SON ' . (new NumeroALetras())->toInvoice(($importeTotalVenta), 2, $invoice->getTipoMoneda() == 'PEN' ? 'soles' : 'dolares'))
        ];
        foreach ($legends as $legend) {
            $pushLgnd = (new Legend())
                ->setCode($legend['code']) // Monto en letras - Catalog. 52
                ->setValue($legend['value']);
            array_push($invoiceLegends, $pushLgnd);
        }

        $invoice->setDetails($invoiceItems)
            ->setLegends($invoiceLegends);


        Log::info('-------------------------------------------------------------------');
        Log::info('Factura N° ' . $invoice->getSerie() . '-' . $invoice->getCorrelativo());
        Log::info('Valor de venta: ' . $invoice->getValorVenta());
        Log::info('IGV Valor de venta: ' . $igvValorVenta);
        Log::info('Descuento global: ' . $discount);
        Log::info('Anticipo : ' . $invoice->getTotalAnticipos());
        Log::info('Operaciones agravadas: ' . $invoice->getMtoOperGravadas());
        Log::info('Subtotal: ' . $invoice->getSubTotal());
        Log::info('Importe total de la venta: ' . $invoice->getMtoImpVenta());

        $result = $this->api->sendDoc($invoice);
        // Log::info('Descuentos totales: '.$invoice->getMtoDescuentos());
        // Log::info('Anticipos totales: '.$invoice->getTotalAnticipos());

        if (!$result['status']) {
            return response()->json([
                'response' => $result
            ], 403);
        }
        Log::info('Se recibio respuesta de sunat: ' . $result);
        //Respuesta de sunat es correcta

        //Registrando en DB
        $invoiceModel = $this->bulk($invoice, $company, $client);
        $invoiceModel->document_pdf = $result['files']['pdf'];
        $invoiceModel->document_xml = $result['files']['xml'];
        $invoiceModel->save();

        Log::info('Se guardo documentos');

        $this->bulkLineInvoices($items, $invoiceModel);
        if ($request->has('invoice.detraction') && $request->input('invoice.detraction') != null) $this->saveDetraction($invoice, $invoiceModel);
        if ($request->input('invoice.payment_method') == 'CRED') $this->bulkQuotes($invoiceModel, $request->input('invoice.quotes'));

        $correlative->invoice_number = $invoiceModel->serie . '-' . $invoiceModel->correlative;
        $correlative->save();
        // Log::info($invoice->getCuotas());
        return response()->json([
            'invoice' => ModelsInvoice::with(['quotes', 'detraction', 'invoicelines'])->get()->find($invoiceModel->id)
        ], 200);
    }

    public function downloadpdf($document)
    {
        try {
            // return response($document);
            return response()->download(storage_path('app/reports/') . $document, $document, [
                'Content-type' => 'application/pdf'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'response' => $th->getMessage()
            ], 403);
        }
    }

    public function downloadxml($document)
    {
        try {
            // return response($document);

            return response()->download(storage_path('app/xmls/') . $document, $document, [
                'Cache-Control' => 'public',
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => 'attachment; filename=test.xml',
                'Content-Transfer-Encoding' => 'binary',
                'Content-type' => 'text/xml'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'response' => $th->getMessage()
            ], 403);
        }
    }

    public function bulk(Invoice $invoice, Company $company, Client $customer)
    {

        $invoiceModel = new ModelsInvoice();
        $invoiceModel->serie = $invoice->getSerie();
        $invoiceModel->correlative = $invoice->getCorrelativo();
        $invoiceModel->date_emission = date_modify($invoice->getFechaEmision(),'-1 day');
        $invoiceModel->date_expiration = $invoice->getFecVencimiento();

        $invoiceModel->company = $company->getRazonSocial();
        $invoiceModel->company_ruc = $company->getRuc();
        $invoiceModel->company_phone = $company->getTelephone();
        $invoiceModel->company_department = $company->getAddress()->getDepartamento();
        $invoiceModel->company_province = $company->getAddress()->getProvincia();
        $invoiceModel->company_district = $company->getAddress()->getDistrito();
        $invoiceModel->company_urbanization = $company->getAddress()->getUrbanizacion();
        $invoiceModel->company_address = $company->getAddress()->getDireccion();

        $invoiceModel->customer = $customer->getRznSocial();
        $invoiceModel->customer_ruc = $customer->getNumDoc();
        $invoiceModel->customer_phone = $customer->getTelephone();
        if ($customer->getAddress() !== null) {
            $invoiceModel->customer_department = $customer->getAddress()->getDepartamento();
            $invoiceModel->customer_province = $customer->getAddress()->getProvincia();
            $invoiceModel->customer_district = $customer->getAddress()->getDistrito();
            $invoiceModel->customer_urbanization = $customer->getAddress()->getUrbanizacion();
            $invoiceModel->customer_address = $customer->getAddress()->getDireccion();
        }


        $invoiceModel->payment_method = $invoice->getFormaPago()->getTipo();
        $invoiceModel->monto_operaciones_agravadas = floatval($invoice->getMtoOperGravadas());
        $invoiceModel->igv = floatval($invoice->getMtoIGV());
        $invoiceModel->total_tributes = floatval($invoice->getTotalImpuestos());
        $invoiceModel->valor_venta = floatval($invoice->getValorVenta());
        $invoiceModel->icbper = floatval($invoice->getIcbper());
        $invoiceModel->subtotal = floatval($invoice->getSubTotal());
        $invoiceModel->monto_importe_venta = floatval($invoice->getMtoImpVenta());
        $invoiceModel->currency = $invoice->getTipoMoneda();
        $invoiceModel->advanced = floatval($invoice->getTotalAnticipos());
        $invoiceModel->discount = floatval($invoice->getMtoDescuentos());
        $invoiceModel->other_charges = floatval($invoice->getSumOtrosCargos());
        $invoiceModel->other_tributes = floatval($invoice->getMtoOtrosTributos());

        return $invoiceModel;
    }

    public function bulkLineInvoices(array $lines, ModelsInvoice $invoice)
    {
        foreach ($lines as $line) {
            $invoiceLine = new InvoiceLine();
            $invoiceLine->id_invoice = $invoice->id;
            $invoiceLine->quantity = $line['quantity'];
            $invoiceLine->measurement_unit = $line['measurement_unit'];
            $invoiceLine->unit_value = $line['unit_value'];
            $invoiceLine->description = $line['description'];
            $invoiceLine->icbper = $line['icbper'];
            $invoiceLine->save();
        }
    }



    public function saveDetraction(Invoice $invoice, ModelsInvoice $invoiceModel)
    {
        $detraction = new ModelsDetraction();
        $detraction->id_invoice = $invoiceModel->id;
        $detraction->cod_bien_producto = $invoice->getDetraccion()->getCodBienDetraccion();
        $detraction->cod_medio_pago = $invoice->getDetraccion()->getCodMedioPago();
        $detraction->cuenta_banco = $invoice->getDetraccion()->getCtaBanco();
        $detraction->porcentaje = $invoice->getDetraccion()->getPercent();
        $detraction->amount = floatval($invoice->getDetraccion()->getMount());
        $detraction->save();
    }

    public function bulkQuotes(ModelsInvoice $invoice, array $quotes)
    {

        foreach ($quotes as $q) {

            $quote = new InvoiceQuote();
            $quote->id_invoice = $invoice->id;
            $quote->amount = floatval($q['amount']);
            $quote->currency = $q['currency'];
            $quote->payment_date = new DateTime($q['payment_date']);

            $quote->save();
        }
    }

    public function getCorrelative()
    {
        $correlatives = CorrelativeService::all();
        $nextCorrelative = null;
        if (!$correlatives->isEmpty()) {
            $correlative = CorrelativeService::latest()->first();
            if ($correlative->invoice_number == null) {
                $nextCorrelative = $correlative->id;
            } else {
                $correlative = new CorrelativeService();
                $correlative->save();
                $nextCorrelative = $correlative->id;
            }
        } else {
            $correlative = new CorrelativeService();
            $correlative->save();
            $nextCorrelative = $correlative->id;
        }
        return ['correlativeModel' => $correlative, 'nextCorrelative' => $nextCorrelative];
    }

    public function generateBillsOfRecurrentes()
    {
        $spc = new SharepointController();
        $recurrentes = $spc->getRecurrentes();
        foreach ($recurrentes as $r) {
            $linesBill = $spc->getItems($r->getProperty('ID'));
            $invoice = $this->handleItemsListToInvoice($r, $linesBill);

            /**
             * Si no existe el cliente
             * no emitir y continuar bucle
             */
            if($invoice == null){

                continue;
            }
            $result = $this->api->sendDoc($invoice);

            if ($result['status']) {
                $invoiceModel = $this->bulk($invoice, $invoice->getCompany(), $invoice->getClient());
                $invoiceModel->document_pdf = $result['files']['pdf'];
                $invoiceModel->document_xml = $result['files']['xml'];
                $invoiceModel->save();

                foreach ($linesBill as $line) {
                    $invoiceLine = new InvoiceLine();
                    $invoiceLine->id_invoice = $invoiceModel->id;
                    $invoiceLine->quantity = $line->getProperty('Quantity');
                    $invoiceLine->measurement_unit = "NIU";
                    $invoiceLine->unit_value = $line->getProperty('Unit_value');
                    $invoiceLine->description = $line->getProperty('Detail');
                    $invoiceLine->icbper = 0.00;
                    $invoiceLine->save();
                }
                if ($r->getProperty('withDetraction') == true) $this->saveDetraction($invoice, $invoiceModel);
                if ($r->getProperty('Type_invoiceOrder') == 'CREDITO')
                {
                        $quote = new InvoiceQuote();
                        $quote->id_invoice = $invoiceModel->id;
                        $amount = $invoice->getMtoImpVenta();;
                        if(boolval($r->getProperty('withDetraction'))==true){
                            $amount = floatval($invoice->getMtoImpVenta() - $invoice->getDetraccion()->getMount());
                        }
                        $quote->amount = $amount;
                        $quote->currency = $r->getProperty('Currency') == 'SOLES' ? 'PEN' : 'USD';
                        $quote->payment_date = $invoice->getCuotas()[0]->getFechaPago();
                        $quote->save();
                }
                $correlative = CorrelativeService::latest()->first();
                $correlative->invoice_number = $invoiceModel->serie . '-' . $invoiceModel->correlative;
                $correlative->save();

                //Send message
                $customer = (new SharepointController)->getCustomer($invoiceModel->customer_ruc)[0];
                Mail::to($customer->getProperty('Email'))->send(new \App\Mail\MailCobranzas($invoiceModel,$customer));
            }else{
                Log::info($result['message']);
            }
        }
    }


    public function handleItemsListToInvoice($recurrente, $lines)
    {
        $discount = $recurrente->getProperty('Discount');
        $advanced = $recurrente->getProperty('Advanced');
        $isc = $recurrente->getProperty('Isc');
        $other_charges = $recurrente->getProperty('OtrosCargos');
        $sumatoriaValoresItems = 0;
        $invoiceItems = [];
        $icbper = 0;
        foreach ($lines as $item) {
            $totalItem = ($item->getProperty('Unit_value')) * ($item->getProperty('Quantity'));
            $igv = ($totalItem) * 0.18;
            $sumatoriaValoresItems = $sumatoriaValoresItems + $totalItem;
            $saleDetail = (new SaleDetail())
                ->setCodProducto('P001')
                ->setUnidad('NIU') // Unidad - Catalog. 03
                ->setCantidad($item->getProperty('Quantity'))
                ->setMtoValorUnitario($item->getProperty('Unit_value'))
                ->setDescripcion($item->getProperty('Detail'))
                ->setMtoBaseIgv($item->getProperty('Unit_value') * $item->getProperty('Quantity')) //cantidad * precio unitario
                ->setIcbper(0.00)
                ->setPorcentajeIgv(18.00) // 18%
                ->setIgv($igv)
                ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos($igv) // Suma de impuestos en el detalle
                ->setMtoValorVenta($item->getProperty('Unit_value') * $item->getProperty('Quantity'))
                ->setMtoPrecioUnitario(round($item->getProperty('Unit_value') * 1.18, 2));
            // ;
            array_push($invoiceItems, $saleDetail);
            $icbper = $icbper + 0.00;
        }

        $customer = (new SharepointController)->getCustomer($recurrente->getProperty('ruc_customer'))[0];

        //Si no existe cliente registrado con ruc
        if($customer == null){
            Log::error("Customer is not defined on Customer list sharepoint with Ruc ".$recurrente->getProperty('ruc_customer'));

            Mail::send('mail/Notify',[
                'customer'=>$recurrente->getProperty('Bussines_name_customer'),
                'lines'=>$lines,
                'exception'=>"El ruc {$recurrente->getProperty('ruc_customer')} no se encuentra registrado en Cobranzas"
            ],function($message){
                $message->to("nfigueroa@mobiledatas.com");
            });

            return null;
        }
        $clientAddress = (new Address)
            ->setDepartamento($customer->getProperty('Department'))
            ->setProvincia($customer->getProperty('Province'))
            ->setDistrito($customer->getProperty('District'))
            ->setUrbanizacion($customer->getProperty('Urbanization'))
            ->setDireccion($customer->getProperty('Address'));
        $client = (new Client())
            ->setTipoDoc('6') //Ruc
            ->setNumDoc($customer->getProperty('RUC_customer'))
            ->setTelephone($customer->getProperty('Phone'))
            ->setRznSocial($customer->getProperty('Bussines_name'))
            ->setAddress($clientAddress);


        $address = (new Address())
            ->setUbigueo('150101')
            ->setDepartamento("LIMA")
            ->setProvincia("LIMA")
            ->setDistrito("SAN BORJA")
            ->setUrbanizacion("-")
            ->setDireccion("Av. San Luis 1502-San Borja")
            ->setCodLocal('0000');  // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

        $company = (new Company())
            ->setRuc($recurrente->getProperty('RUC_mds'))
            ->setTelephone("+51 17390932")
            ->setNombreComercial($recurrente->getProperty('Bussines_name_mds'))
            ->setRazonSocial($recurrente->getProperty('Bussines_name_mds'))
            ->setAddress($address);
        $arrayCorrelative = $this->getCorrelative();
        $nextCorrelative = $arrayCorrelative['nextCorrelative'];
        $correlative = $arrayCorrelative['correlativeModel'];

        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion(boolval($recurrente->getProperty('withDetraction')) == true ? '1001' : '0101') // Venta - Catalog. 51
            ->setTipoDoc('01') // Factura - Catalog. 01
            ->setSerie('F001')
            ->setCorrelativo($nextCorrelative)
            ->setFechaEmision((new DateTime())->modify('+1 day')) // Zona horaria: Lima

            ->setTipoMoneda($recurrente->getProperty('Currency') == 'SOLES' ? 'PEN' : 'USD') // Sol - Catalog. 02
            ->setCompany($company)
            ->setClient($client)
            ->setDescuentos([]);
        if ($discount > 0.00) {
            $invoice->setDescuentos([
                (new Charge)
                    ->setCodTipo('02')
                    ->setMontoBase($discount)
                    ->setFactor(1)
                    ->setMonto($discount)
            ]);
            $invoice->setMtoDescuentos($discount);
        }
        if ($other_charges > 0.00) {
            $invoice->setCargos([
                (new Charge)
                    ->setCodTipo('47')
                    ->setMontoBase($other_charges)
                    ->setFactor(1)
                    ->setMonto($other_charges)
            ]);
        }

        if ($advanced > 0.00) {
            $invoice->setDescuentos([
                ...$invoice->getDescuentos(),
                (new Charge)
                    ->setCodTipo('04')
                    ->setFactor(1)
                    ->setMonto($advanced)
                    ->setMontoBase($advanced)
            ])
                ->setAnticipos([
                    (new Prepayment)
                        ->setTipoDocRel('02')
                        ->setNroDocRel($invoice->getSerie() . '-' . $invoice->getCorrelativo())
                        ->setTotal($advanced)
                ])
                ->setTotalAnticipos($advanced);
        }


        $igvValorVenta = $sumatoriaValoresItems * 0.18; //OK
        $subtotal = ($sumatoriaValoresItems - $discount) * 1.18; //OK
        $mtoOperGravadas = ($sumatoriaValoresItems - ($discount + $advanced)); //OK
        $importeTotalVenta = ($subtotal - $advanced);

        $invoice
            ->setMtoOperGravadas(round($mtoOperGravadas, 2))
            ->setMtoIGV(round($mtoOperGravadas * 0.18, 2))
            ->setValorVenta(round($sumatoriaValoresItems - $discount, 2))
            ->setTotalImpuestos(round(($mtoOperGravadas * 0.18) + $isc + $other_charges, 2))
            ->setSubTotal(round(($sumatoriaValoresItems - $discount) * 1.18, 2))
            ->setMtoImpVenta(round($importeTotalVenta, 2));
        $base = 0;

        if (boolval($recurrente->getProperty('withDetraction')) == true) {
            $base = round(($importeTotalVenta) * Bill::getPercentPerCodeDetraction($recurrente->getProperty('CodeDetraction')) / 100, 2);


            // Log::info(Bill::getPercentPerCodeDetraction($request->input('invoice.detraction.cod_bien_detraction')) . " %");

            // Log::info('Porcentaje de detraccion resultado (base): '.$invoice->getTipoMoneda().' '.$base);
            // $detract = $base  * ($invoice->getTipoMoneda() == 'USD' ? 3.97:1);
            // $detract = $base;
            $amountDetract = $base;
            if ($invoice->getTipoMoneda() == 'USD') {
                $exchangerate = $this->api->getExchangeRateValue($invoice->getFechaEmision())['venta'];
                $amountDetract = round($exchangerate * floatval($exchangerate), 2);
            }
            $invoice->setDetraccion(
                (new Detraction)
                    ->setCodBienDetraccion($recurrente->getProperty('CodeDetraction'))
                    ->setCodMedioPago("009")
                    ->setCtaBanco("00781253073")
                    ->setPercent(Bill::getPercentPerCodeDetraction($recurrente->getProperty('CodeDetraction')))
                    ->setMount($amountDetract)
            );
            // Log::info('Monto dolares de detraccion ' . $invoice->getTipoMoneda() . ' ' . $base);
            // Log::info('Monto soles de detraccion PEN ' . $amountDetract);
        }
        if ($recurrente->getProperty('Type_invoiceOrder') == 'CREDITO') {

            // Log::info("Monto pendiente de pago: " . ($invoice->getMtoImpVenta() - $base));

            $invoice->setFormaPago((new FormaPagoCredito($invoice->getMtoImpVenta() - $base)));
            $pushQuo = [(new Cuota())
                ->setMonto($invoice->getMtoImpVenta() - $base)
                ->setMoneda($recurrente->getProperty('Currency') == 'SOLES' ? 'PEN' : 'USD')
                ->setFechaPago((new DateTime())->modify("+{$recurrente->getProperty('DaysToExpiration')} days"))];

            $invoice->setCuotas($pushQuo);
        }

        if ($recurrente->getProperty('Type_invoiceOrder') == 'CONTADO') {
            $invoice->setFormaPago((new FormaPagoContado()));
        }

        $invoiceLegends = [
            (new Legend())
                ->setCode('1000')
                //->setValue('SON ' . (new NumeroALetras())->toInvoice(($importeTotalVenta - $base), 2, $invoice->getTipoMoneda() == 'PEN' ? 'soles' : 'dolares'))
                ->setValue('SON ' . (new NumeroALetras())->toInvoice(($importeTotalVenta), 2, $invoice->getTipoMoneda() == 'PEN' ? 'soles' : 'dolares'))
        ];
        $invoice->setDetails($invoiceItems)
            ->setLegends($invoiceLegends);

        return $invoice;
    }
}
