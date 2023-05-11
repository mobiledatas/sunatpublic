<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:ser="http://service.sunat.gob.pe">
 <soapenv:Header>
 <wsse:Security soapenv:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
 <wsse:UsernameToken>
 <wsse:Username>{{ $username }}</wsse:Username>
 <wsse:Password>{{$password}}</wsse:Password>
 </wsse:UsernameToken>
 </wsse:Security>
 </soapenv:Header>
 <soapenv:Body>
 <ser:getStatusCdr>
 <rucComprobante>{{ $ruc }}</rucComprobante>
 <tipoComprobante>{{ $type }}</tipoComprobante>
 <serieComprobante>{{ $serie }}</serieComprobante>
 <numeroComprobante>{{ $bill }}</numeroComprobante>
 </ser:getStatusCdr>
 </soapenv:Body>
</soapenv:Envelope>
