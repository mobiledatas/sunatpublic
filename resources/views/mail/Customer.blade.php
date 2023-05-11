<!DOCTYPE html>
<html>

<head>
  <title>Mobiledatas.com</title>
</head>

<body>
  <h1>Srs.: {{ $customer->getProperty('Bussines_name') }}</h1>
      <p>
        Estimado Cliente, en Mobile Data Solutions seguimos innovando para servirle mejor. Ahora, Ud. puede descargar e imprimir sus Facturas Electrónicas desde la comodidad de su computadora ingresando a www.sunat.gob.pe con su Clave SOL.
        <br>
        Adjunto encontrará la representación impresa de la Factura Electrónica, consulte en www.sunat.gob.pe
        Para consultar la validez de su Factura Electrónica ingrese a:
        <br>
        <a href="http://www.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm">http://www.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm</a> 
        <br>
        Se le envía el documento relacionado a la Factura Electrónica <strong> # {{$invoice->serie}}-{{$invoice->correlative}}</strong>
        <br>
        Agradeceremos realizar sus pagos a nuestras cuentas bancarias: y el envío de las constancias.
        <br>
        <br>
        
        <mark> CTA CORRIENTE SOLES - BCP: 194-9403571-0-64 <br></mark>
        <mark>Código de Cuenta Interbancario: 00219400940357106492<br></mark>
        <mark>CTA CORRIENTE DOLARES - BCP: 194-9400445-1-98<br></mark>
        <mark>Código de Cuenta Interbancario: 00219400940044519898<br></mark>
        <mark>CTA DETRACCION - NACION: 00-781-253073<br></mark>
      </p>
</body>

</html>
