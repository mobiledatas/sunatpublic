<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Support\Facades\Log;

use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Report\HtmlReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\Report\PdfReport;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Support\Facades\File;

use function PHPSTORM_META\type;

class ApiSunatController extends Controller
{
    //
    private $url = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
    private $user = 'E';
    private $ruc = '2';
    private $pass = '';
    private See $see;
    private array $result = [
        'status' => false,
        'type' => 'error'
    ];
    function __construct()
    {
        $this->see = new See();
        $this->see->setCertificate(file_get_contents(storage_path('certificate/') . 'certificado_mds.pem'));
        $this->see->setService(SunatEndpoints::FE_PRODUCCION);
        $this->see->setClaveSOL($this->ruc,'','');

    }

    public function sendDoc(Invoice | Note $document)
    {
        try {

            // // Cliente
            // $client = (new Client())
            //     ->setTipoDoc('6')
            //     ->setNumDoc('20000000001')
            //     ->setRznSocial('EMPRESA X');

            // // Emisor
            // $address = (new Address())
            //     ->setUbigueo('150101')
            //     ->setDepartamento('LIMA')
            //     ->setProvincia('LIMA')
            //     ->setDistrito('LIMA')
            //     ->setUrbanizacion('-')
            //     ->setDireccion('Av. Villa Nueva 221')
            //     ->setCodLocal('0000'); // Codigo de establecimiento asignado por SUNAT, 0000 por defecto.

            // $company = (new Company())
            //     ->setRuc('20123456789')
            //     ->setRazonSocial('MOBILE DATA SOLUTIONS SAC')
            //     ->setNombreComercial('GREEN')
            //     ->setAddress($address);

            // // Venta
            // $invoice = (new Invoice())
            //     ->setUblVersion('2.1')
            //     ->setTipoOperacion('0101') // Venta - Catalog. 51
            //     ->setTipoDoc('01') // Factura - Catalog. 01
            //     ->setSerie('F001')
            //     ->setCorrelativo('1')
            //     ->setFechaEmision(new DateTime('2020-08-24 13:05:00-05:00')) // Zona horaria: Lima
            //     ->setFormaPago((new FormaPagoContado())->setTipo('Contado')) // FormaPago: Contado
            //     ->setTipoMoneda('PEN') // Sol - Catalog. 02
            //     ->setCompany($company)
            //     ->setClient($client)
            //     ->setMtoOperGravadas(100.00)
            //     ->setMtoIGV(18.00)
            //     ->setTotalImpuestos(18.00)
            //     ->setValorVenta(100.00)
            //     ->setSubTotal(118.00)
            //     ->setMtoImpVenta(118.00);

            // $item = (new SaleDetail())
            //     ->setCodProducto('P001')
            //     ->setUnidad('NIU') // Unidad - Catalog. 03
            //     ->setCantidad(2)
            //     ->setMtoValorUnitario(50.00)
            //     ->setDescripcion('PRODUCTO 1')
            //     ->setMtoBaseIgv(100)
            //     ->setPorcentajeIgv(18.00) // 18%
            //     ->setIgv(18.00)
            //     ->setTipAfeIgv('10') // Gravado Op. Onerosa - Catalog. 07
            //     ->setTotalImpuestos(18.00) // Suma de impuestos en el detalle
            //     ->setMtoValorVenta(100.00)
            //     ->setMtoPrecioUnitario(59.00);

            // $legend = (new Legend())
            //     ->setCode('1000') // Monto en letras - Catalog. 52
            //     ->setValue('SON DOSCIENTOS TREINTA Y SEIS CON 00/100 SOLES');

            // $invoice->setDetails([$item])
            //     ->setLegends([$legend]);

            $result = $this->see->send($document);

            File::put(storage_path('app/xmls/') . $document->getName() . '.xml', $this->see->getFactory()->getLastXml());
            // $xml = $this->see->getXmlSigned($document);
            // Log::info($xml);
            // File::put(storage_path('app/xmls/').$document->getName().'.xml',$xml);
            $response = $result->isSuccess();
            if ($response) {
                //Guardando CDR
                File::put(storage_path('app/cdr/') . $document->getName() . '.zip', $result->getCdrZip());
                $cdr = $result->getCdrResponse();

                $code = (int) $cdr->getCode();

                if ($code == 0) {
                    if (count($cdr->getNotes()) > 0) {
                    }
                    $file = $this->generateReport($document);
                    $this->result['status'] = true;
                    $this->result['type'] = 'success';
                    $this->result['message'] = 'El comprobante a sido enviado y aprobado exitosamente';
                    // $this->result[] = ;
                    $this->result['files'] = [
                        'pdf'=>$file,
                        'xml'=>$document->getName().'.xml'
                    ];
                    return $this->result;
                } else if ($code >= 2000 && $code <= 3999) {

                    $this->result['type'] = 'warning';
                    $this->result['message'] = 'El documento no a sido aprobado';
                    return $this->result;
                } else {
                    $this->result['message'] = 'Ocurrio un error en el servicio web';
                    return $this->result;
                }
            } else {
                $this->result['message'] = [
                    'code_error' => $result->getError()->getCode(),
                    'message' => 'Sunat respondió: ' . $result->getError()->getMessage()
                ];
                return $this->result;
            }
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . ' | ' . __METHOD__ . ': ' . $th->getMessage());
            $this->result['type'] = 'warning';
            $this->result['message'] = $th->getMessage();
            return $this->result;
        }
    }

    public function getStatusCdr($bill, $ruc, $type, $serie)
    {

    }


    public function generateReport(Invoice | Note $doc)
    {
        $twigOptions = [
            'cache' => base_path() . '/cache',
            'strict_variables' => true,
        ];
    $report = new HtmlReport(base_path('/resources/views/twig/factura'));
        $resolver = new DefaultTemplateResolver();

        $report->setTemplate(
            // $resolver->getTemplate($invoice)
            'factura.html.twig'
        );

        $pdfReport = new PdfReport($report);
        $pdfReport->setOptions( [
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
        ]);
        $pdfReport->setBinPath(storage_path('wkhtmltopdf/bin/wkhtmltopdf.exe'));
        $params = [
            'system' => [
                'logo'=>file_get_contents(storage_path('img/logo-mds.png')),
                'hash' => ""
            ],
            'user' => [
                'header' => 'Telf: +51 17390932',
                'extras'     => [
                    // Leyendas adicionales
                    // ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => 'Mobile Data Solutions S.A.C'],
                ],
                'footer' => ''
            ]
        ];
        // $html = $report->render($doc,$params);
        // Log::info(($doc->getCuotas()));
        // Log::info($html);
        // Log::info(print_r($doc,true));
        // Log::info(var_dump($doc));
        $pdf = $pdfReport->render($doc,$params);
        // Log::info(var_dump($doc));
        File::put(storage_path('app/reports/').$doc->getName().'.pdf',$pdf);
        return $doc->getName().'.pdf';
    }

    public function getExchangeRateValue(DateTime $date)
    {
        $date = date('Y-m-d');
        // return $date;
        $client = new GuzzleHttpClient();
        $response = $client->get("https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha={$date}",[
            'headers'=>[
                'Referer'=>'https://apis.net.pe/tipo-de-cambio-sunat-api',
                'Authorization'=>' Bearer apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N'
            ]
        ]);

        return json_decode($response->getBody()->getContents(),true);
    }
}
