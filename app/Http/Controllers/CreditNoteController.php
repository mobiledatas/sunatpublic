<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillRequest;
use App\Http\Requests\NoteRequest;
use App\Models\Bill;
use App\Models\CorrelativeServiceNotas;
use App\Models\Invoice as ModelsInvoice;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Luecano\NumeroALetras\NumeroALetras;

use function PHPUnit\Framework\isEmpty;

class CreditNoteController extends Controller
{

    //
    private ApiSunatController $service;

    public function __construct()
    {
        $this->service = new ApiSunatController();
    }

    public function store(NoteRequest $request)
    {
        //Log::info('-------------------------------');
        //Log::info('Into CrediNoteController: ');
        $invoice = ModelsInvoice::with('quotes', 'invoicelines', 'detraction')->where('serie', $request->input('invoice.serie'))->where('correlative', $request->input('invoice.correlative'))->get()->first();
        //Log::info(json_encode($invoice,JSON_PRETTY_PRINT));
        // return response()->json($invoice);
        $customer = (new Client())
            ->setTipoDoc('6') //Ruc
            ->setNumDoc($request->input('customer.ruc'))
            ->setTelephone($request->input('customer.phone'))
            ->setRznSocial($request->input('customer.name'));

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
        $crl     = $this->getCorrelative();
        $correlative = $crl['correlativeModel'];
        $nexCorrelative = $crl['nextCorrelative'];
        $note = new Note();
        //Log::info($request->input('invoice.serie') . '-' . $request->input('invoice.correlative'));
        $note

            ->setUblVersion('2.1')
            ->setTipoDoc('07')
            ->setSerie('FF01')
            ->setCorrelativo($nexCorrelative)
            ->setFechaEmision((new DateTime($request->input('note.date_emission')))->modify('+1 day'))

            ->setTipDocAfectado('01') // Tipo Doc: Factura
            ->setNumDocfectado($request->input('invoice.serie') . '-' . $request->input('invoice.correlative')) // Factura: Serie-Correlativo
            ->setCodMotivo($request->input('note.code')) // Catalogo. 09
            ->setDesMotivo($request->input('note.description'))
            ->setTipoMoneda($request->input('note.currency'))
            ->setCompany($company)
            ->setClient($customer)
            ->setMtoOperGravadas($invoice->monto_operaciones_agravadas)
            ->setMtoIGV($invoice->igv)
            ->setTotalImpuestos($invoice->igv)
            //->setMtoDiscount($invoice->discount)
            ->setMtoImpVenta($invoice->monto_importe_venta);
        $base = 0;


        if (isset($invoice->detraction)) {
            //Log::info($invoice->detraction->cod_bien_producto);
            $base = round($invoice->monto_importe_venta * (Bill::getPercentPerCodeDetraction($invoice->detraction->cod_bien_producto) / 100), 2);
        }

        //forma de pago solucion pago al contado ######
        //$note->setFormaPago(new FormaPagoContado());

        //invoice tiene cuotas?  --- las cuotas no estan vacias?
        if (isset($invoice->quotes) && !$invoice->quotes->isEmpty()) {
            $note->setFormaPago((new FormaPagoCredito($invoice->monto_importe_venta - $base)));
            $quotes = [];
            foreach ($invoice->quotes  as $q) {
                array_push($quotes, (new Cuota)
                    ->setMoneda($invoice->currency)
                    ->setMonto($q->amount)
                    ->setFechaPago(new DateTime($q->payment_date)));
            }
            $note->setCuotas($quotes);
        }

        $lines = [];
        $count = 0;
        //$descuento=$invoice->discount;

        //Log::info(json_encode($invoice->invoicelines,JSON_PRETTY_PRINT));
        foreach ($invoice->invoicelines as $line) {
            array_push($lines, (new SaleDetail)
                ->setUnidad($line->measurement_unit) // Unidad - Catalog. 03
                ->setCantidad($line->quantity)
                ->setMtoValorUnitario($line->unit_value)
                ->setDescripcion($line->description)
                ->setMtoBaseIgv($line->quantity * $line->unit_value) //cantidad * precio unitario
                ->setIcbper($line->icbper)
                ->setPorcentajeIgv(18.00) // 18%
                ->setIgv(round(($line->quantity * $line->unit_value) * 0.18, 2))
                ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
                ->setTotalImpuestos(round(($line->quantity * $line->unit_value) * 0.18, 2)) // Suma de impuestos en el detalle
                ->setMtoValorVenta($line->quantity * $line->unit_value)
                ->setMtoPrecioUnitario(round($line->unit_value * 1.18, 2)));
            // ->setCodProducto('P001'))
            $count = $count + $line->quantity * $line->unit_value;
            //Log::info('Monto total valor venta de cada item: ' . $count);
        }
        //$count=$count-$descuento;
        $note->setMtoOperGravadas($count)
            ->setMtoIGV(round($count * 0.18, 2))
            ->setTotalImpuestos(round($count * 0.18, 2))

            ->setMtoImpVenta(round($count * 1.18, 2));

        //Log::info('Monto total valor venta de cada item: ' . $count);
        //Log::info(json_encode($lines,JSON_PRETTY_PRINT));
        $note->setLegends([
            (new Legend)->setCode('1000')
                ->setValue('SON ' . (new NumeroALetras())->toInvoice(($invoice->monto_importe_venta - $base), 2, $invoice->currency == 'PEN' ? 'SOLES' : 'DOLARES'))
                //->setValue('SON ' . (new NumeroALetras())->toInvoice(($invoice->monto_importe_venta), 2, $invoice->currency == 'PEN' ? 'SOLES' : 'DOLARES'))
        ]);
        $note->setDetails($lines);
        $result = $this->service->sendDoc($note);
        if (!$result['status']) {
            return response()->json([
                'response' => $result
            ], 403);
            //https://github.com/mobiledatas/sunat.git
        }
        $doc = $this->bulkNote($note, $company, $customer, $invoice);
        $doc->document_pdf = $result['files']['pdf'];
        $doc->document_xml = $result['files']['xml'];
        $doc->save();
        $correlative->note_number = $note->getSerie() . '-' . $note->getCorrelativo();
        $correlative->save();
        return response()->json([
            'note' => ModelsInvoice::find($doc->id),
            'invoice' => ModelsInvoice::with(['quotes', 'detraction', 'invoicelines'])->get()->find($invoice->id)
        ], 200);
    }

    public function bulkNote(Note $note, Company $company, Client $customer, $related)
    {
        $invoiceModel = new ModelsInvoice();
        $invoiceModel->serie = $note->getSerie();
        $invoiceModel->correlative = $note->getCorrelativo();
        $invoiceModel->date_emission = $note->getFechaEmision();
        $invoiceModel->date_expiration = null;

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


        $invoiceModel->payment_method = $related->payment_method;
        $invoiceModel->monto_operaciones_agravadas = floatval($related->monto_operaciones_agravadas);
        $invoiceModel->igv = floatval($related->igv);
        $invoiceModel->total_tributes = floatval($related->total_tributes);
        $invoiceModel->valor_venta = floatval($related->valor_venta);
        $invoiceModel->icbper = floatval($related->icbper);
        $invoiceModel->subtotal = floatval($related->subtotal);
        $invoiceModel->monto_importe_venta = floatval($related->monto_importe_venta);
        $invoiceModel->currency = $related->currency;
        $invoiceModel->advanced = floatval($related->advanced);
        $invoiceModel->discount = floatval($related->discount);
        $invoiceModel->other_charges = floatval($related->other_charges);
        $invoiceModel->other_tributes = floatval($related->other_tributes);
        $invoiceModel->doc_related = $related->serie . '-' . $related->correlative;
        return $invoiceModel;
    }
    public function getCorrelative()
    {
        $correlatives = CorrelativeServiceNotas::all();
        $nextCorrelative = null;
        if (!$correlatives->isEmpty()) {
            $correlative = CorrelativeServiceNotas::latest()->first();
            if ($correlative->note_number == null) {
                $nextCorrelative = $correlative->id;
            } else {
                $correlative = new CorrelativeServiceNotas();
                $correlative->save();
                $nextCorrelative = $correlative->id;
            }
        } else {
            $correlative = new CorrelativeServiceNotas();
            $correlative->save();
            $nextCorrelative = $correlative->id;
        }
        return ['correlativeModel' => $correlative, 'nextCorrelative' => $nextCorrelative];
    }
}
