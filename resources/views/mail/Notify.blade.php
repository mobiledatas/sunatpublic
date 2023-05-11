<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mobiledatas.com</title>
</head>
<body>
    <h1>Notificacion de excepcion</h1>
    <p>No se emitio la orden correspondiente al cliente <strong> {{ $customer }}</strong> por la siguiente excepcion:</p>
    <p style="font-style: italic">
        {{ $exception }}
    </p>
    <p>Orden de concepto:</p>
    <p>
        Detalles de productos o servicios
    </p>
    <ul>
        @foreach ($lines as $line)
        <li>
            {{ $line->getProperty('Detail') }}
        </li>
        @endforeach     
    </ul>
    
</body>
</html>