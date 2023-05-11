<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
  xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
  xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
  xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
  xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
  xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
  xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <ext:UBLExtensions>
    <ext:UBLExtension>
      <ext:ExtensionContent>
        <ds:Signature Id="signatureKG">
          <ds:SignedInfo>
            <ds:CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments" />
            <ds:SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#dsa-sha1" />
            <ds:Reference URI="">
              <ds:Transforms>
                <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature" />
              </ds:Transforms>
              <ds:DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1" />
              <ds:DigestValue>+pruib33lOapq6GSw58GgQLR8VGIGqANloj4EqB1cb4=</ds:DigestValue>
            </ds:Reference>
          </ds:SignedInfo>
          <ds:SignatureValue>Oatv5xMfFInuGqiX9SoLDTy2yuLf0tTlMFkWtkdw1z/Ss6kiDz+vIgZhgKfIaxp+JbVy57
            GT52f10VLMLatdwPVRbrWmz1/NIy5CWp1xWMaM6fC/9SXV0O1Lqopk0UeX2I2yuf05QhmVfjgUu6GnS3m6
            o6zM9J36iDvMVZyj7vbJTwI8SfWjTSNqxXlqPQ==</ds:SignatureValue>
          <ds:KeyInfo>
            <ds:X509Data>
              <ds:X509Certificate>MIIF9TCCBN2gAwIBAgIGAK0oRTg/MA0GCSqGSIb3DQEBCwUAMFkxCzAJBgNVBAYTAlRSMUowSAYDVQQDDEFNYWxpIE3DvGjDvHIgRWxla3Ryb25payBTZXJ0aWZpa2EgSGl6bWV0IFNhxJ9sYXnEsWPEsXPEsSAtIFRlc3QgMTAeFw0wOTEwMjAxMTM3MTJaFw0xNDEwMTkxMTM3MTJaMIGgMRowGAYDVQQLDBFHZW5lbCBNw7xkw7xybMO8azEUMBIGA1UEBRMLMTAwMDAwMDAwMDIxbDBqBgNVBAMMY0F5ZMSxbiBHcm91cCAtIFR1cml6bSDEsHRoYWxhdCDEsGhyYWNhdCBUZWtzdGlsIMSwbsWfYWF0IFBhemFyiMwtPnC2DRjdsyGv3bxwRZr9wXMRrMNwRjyFe9JPA7bSscEgaXwzDUG5FCvfS/PNT+XCce+VECAx6Q3R1ZRSA49fYz6tDB4Ia5HVBXZODmrCs26XisHF6kuS5N/yGg8E7VC1BRr/SmxXeLTdjQYAfo7lxCz4dT6wP5TOiBvF+lyWW1bi9nbliXyb/e5HjCp4k/ra9LTskjbY/Ukl5O8G9JEAViZkjvxDX7T0yVRHgMGiioIKVMwU6Lrtln607BNurLwED0OeoZ4wBgkBiB5vXofreXrfN2pHZ2=</ds:X509Certificate>
            </ds:X509Data>
          </ds:KeyInfo>
        </ds:Signature>
      </ext:ExtensionContent>
    </ext:UBLExtension>
  </ext:UBLExtensions>
  <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
  <cbc:CustomizationID>2.0</cbc:CustomizationID>
  <cbc:ProfileID schemeName="SUNAT:Identificador de Tipo de Operación" schemeAgencyName="PE:SUNAT"
    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17">0101</cbc:ProfileID>
  <cbc:ID>F001-1</cbc:ID>
  <cbc:IssueDate>2017-05-14</cbc:IssueDate>
  <cbc:IssueTime>13:25:51</cbc:IssueTime>
  <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="SUNAT:Identificador 
de Tipo de Documento"
    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">01</cbc:InvoiceTypeCode>
  <cbc:Note languageLocaleID="1000">CUATROCIENTOS VEINTITRES MIL DOSCIENTOS
    VEINTICINCO Y 00/100</cbc:Note>
  <cbc:Note languageLocaleID="3000">0501002017051400452</cbc:Note>
  <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency"
    listAgencyName=" United Nations Economic Commission for 
Europe">PEN</cbc:DocumentCurrencyCode>
  <cbc:LineCountNumeric>5</cbc:LineCountNumeric>
  <cac:OrderReference>
    <cbc:ID>7852166</cbc:ID>
  </cac:OrderReference>
  <cac:Signature>
    <cbc:ID>IDSignSP</cbc:ID>
    <cac:SignatoryParty>
      <cac:PartyIdentification>
        <cbc:ID>20100454523</cbc:ID>
      </cac:PartyIdentification>
      <cac:PartyName>
        <cbc:Name>SOPORTE TECNOLOGICO EIRL</cbc:Name>
      </cac:PartyName>
    </cac:SignatoryParty>
    <cac:DigitalSignatureAttachment>
      <cac:ExternalReference>
        <cbc:URI>#SignatureSP</cbc:URI>
      </cac:ExternalReference>
    </cac:DigitalSignatureAttachment>
  </cac:Signature>
  <cac:AccountingSupplierParty>
    <cac:Party>
      <cac:PartyName>
        <cbc:Name>Tu Soporte</cbc:Name>
      </cac:PartyName>
      <cac:PartyTaxScheme>
        <cbc:RegistrationName>
          <![CDATA[Soporte Tecnológicos EIRL]]>
        </cbc:RegistrationName>
        <CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de
Identidad" schemeAgencyName="PE:SUNAT"
          schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20100066603</CompanyID>
        <cac:RegistrationAddress>
          <cbc:AddressTypeCode>0000</cbc:AddressTypeCode>
        </cac:RegistrationAddress>
        <cac:TaxScheme>
          <cbc:ID>-</cbc:ID>
        </cac:TaxScheme>
      </cac:PartyTaxScheme>
    </cac:Party>
  </cac:AccountingSupplierParty>
  <cac:AccountingCustomerParty>
    <cac:Party>
      <cac:PartyTaxScheme>
        <cbc:RegistrationName>SERVICABINAS S.A.</cbc:RegistrationName>
        <CompanyID schemeID="6" schemeName="SUNAT:Identificador de Documento de
Identidad" schemeAgencyName="PE:SUNAT"
          schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">20587896411</CompanyID>
        <cac:TaxScheme>
          <cbc:ID>-</cbc:ID>
        </cac:TaxScheme>
      </cac:PartyTaxScheme>
    </cac:Party>
  </cac:AccountingCustomerParty>
  <cac:AllowanceCharge>
    <cbc:ChargeIndicator>False</cbc:ChargeIndicator>
    <cbc:AllowanceChargeReasonCode>00</cbc:AllowanceChargeReasonCode>
    <cbc:MultiplierFactorNumeric>0.05</cbc:MultiplierFactorNumeric>
    <cbc:Amount currencyID="PEN">5.00</cbc:Amount>
    <cbc:BaseAmount currencyID="PEN">5.00</cbc:BaseAmount>
  </cac:AllowanceCharge>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID="PEN">5.00</cbc:TaxableAmount>
      <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
      <cac:TaxCategory>
        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
          schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
        <cac:TaxScheme>
          <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
          <cbc:Name>IGV</cbc:Name>
          <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
  <cac:LegalMonetaryTotal>
    <cbc:LineExtensionAmount currencyID="PEN">5.00</cbc:LineExtensionAmount>
    <cbc:TaxInclusiveAmount currencyID="PEN">5.00</cbc:TaxInclusiveAmount>
    <cbc:AllowanceTotalAmount currencyID="PEN">5.00</cbc:AllowanceTotalAmount>
    <cbc:PayableAmount currencyID="PEN">5.00</cbc:PayableAmount>
  </cac:LegalMonetaryTotal>
  <cac:InvoiceLine>
    <cbc:ID>1</cbc:ID>
    <cbc:InvoicedQuantity unitCode="NIU" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="Europe">2000
    </cbc:InvoicedQuantity>
    <cbc:LineExtensionAmount currencyID="PEN">5.00</cbc:LineExtensionAmount>
    <cac:PricingReference>
      <cac:AlternativeConditionPrice>
        <cbc:PriceAmount currencyID="PEN">5.00</cbc:PriceAmount>
        <cbc:PriceTypeCode listName="SUNAT:Indicador de Tipo de Precio" listAgencyName="PE:SUNAT"
          listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
      </cac:AlternativeConditionPrice>
    </cac:PricingReference>
    <cac:AllowanceCharge>
      <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
      <cbc:AllowanceChargeReasonCode>00</cbc:AllowanceChargeReasonCode>
      <cbc:MultiplierFactorNumeric>0.00</cbc:MultiplierFactorNumeric>
      <cbc:Amount currencyID="PEN">5.00</cbc:Amount>
      <cbc:BaseAmount currencyID="PEN">5.00</cbc:BaseAmount>
    </cac:AllowanceCharge>
    <cac:TaxTotal>
      <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
      <cac:TaxSubtotal>
        <cbc:TaxableAmount currencyID="PEN">0.00</cbc:TaxableAmount>
        <cbc:TaxAmount currencyID="PEN">0.00</cbc:TaxAmount>
        <cac:TaxCategory>
          <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
            schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
          <cbc:Percent>0.00</cbc:Percent>
          <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="SUNAT:Codigo de Tipo de Afectación del IGV"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">10</cbc:TaxExemptionReasonCode>
          <cac:TaxScheme>
            <cbc:ID schemeID="UN/ECE 5153" schemeName="Tax Scheme Identifier"
              schemeAgencyName="United Nations Economic Commission for Europe">1000</cbc:ID>
            <cbc:Name>IGV</cbc:Name>
            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
          </cac:TaxScheme>
        </cac:TaxCategory>
      </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:Item>
      <cbc:Description>Grabadora LG Externo Modelo: GE20LU10</cbc:Description>
      <cac:SellersItemIdentification>
        <cbc:ID>GLG199</cbc:ID>
      </cac:SellersItemIdentification>
      <cac:CommodityClassification>
        <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">52161515
        </cbc:ItemClassificationCode>
      </cac:CommodityClassification>
    </cac:Item>
    <cac:Price>
      <cbc:PriceAmount currencyID="PEN">5.00</cbc:PriceAmount>
    </cac:Price>
  </cac:InvoiceLine>
</Invoice>
