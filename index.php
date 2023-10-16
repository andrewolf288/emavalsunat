<?php

function errorHandler($severity, $message, $file, $line)
{
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
}

// Establece el controlador de errores personalizado
set_error_handler('errorHandler');

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');
// Habilita la captura de errores internos de libxml
libxml_use_internal_errors(true);

// Ruta al archivo XML que deseas procesar
$directorio = 'comprobantes/septiembre';

// Ruta al archivo XSD que se utilizará para validar el XML
$xsdFile = 'maindoc/UBL-Invoice-2.1.xsd';
$xsdFile2 = 'maindoc/UBLPE-Invoice-1.0.xsd';
$archivosNoValidos = [];
$archivosNoCargados = [];
$archivosValidosCount = 0;
$archivosNoCargadosCount = 0;
$archivosInvalidosCount = 0;
$countAux = 0;

foreach (glob($directorio . '/*.[xX][mM][lL]', GLOB_BRACE) as $archivoXML) {
    // extraigo el nombre del archivo
    $nombreArchivo = pathinfo($archivoXML, PATHINFO_BASENAME);
    if (filesize($archivoXML) > 0) {
        $xmlContent = file_get_contents($archivoXML);
        $xml = new DOMDocument();
        $xml->validateOnParse = false;

        // Cargar el XML y validar el esquema solo si la carga es exitosa
        if ($xmlContent !== false) {
            try {
                if ($xml->loadXML($xmlContent)) {
                    // Validar el XML con el esquema XSD
                    $archivosValidosCount++;
                    $xpath = new DOMXPath($xml);

                    // Obtén el valor del atributo currencyID
                    $currencyID = $xpath->query('//cbc:LineExtensionAmount/@currencyID')->item(0)->nodeValue;

                    // Obtén el contenido del tag LineExtensionAmount
                    $lineExtensionAmount = $xpath->query('//cbc:LineExtensionAmount')->item(0)->nodeValue;
                    echo 'Currency ID: ' . $currencyID . '<br>';
                    echo 'Line Extension Amount: ' . $lineExtensionAmount . '<br>';
                    echo 'Nombre XML: ' . $nombreArchivo . '<br>' . '<br>';
                } else {
                    $error = error_get_last();
                    $archivosNoCargados[] = [
                        'archivo' => $nombreArchivo,
                        'error' => $error['message']
                    ];
                }
            } catch (ErrorException $e) {
                $archivosInvalidosCount++;
                $archivosNoValidos[] = array('archivo' => $nombreArchivo, 'errores' => $e->getMessage());
            }
        }
        // Limpia los errores
        libxml_clear_errors();
    } else {
        $archivosNoCargados[] = [
            'archivo' => $nombreArchivo,
            'error' => "El archivo esta vacio"
        ];
    }
}

restore_error_handler();

echo 'Archivos no cargados: ' . '<br>';
print_r($archivosNoCargados);
echo '<br>';
echo '<br>';
echo 'Archivos no validos: ' . '<br>';
print_r($archivosNoValidos);
echo '<br>';
// echo 'Numero de correctos: ' . $archivosValidosCount;
// echo '<br>';
// echo 'Numero de invalidos: ' . $archivosInvalidosCount;
// echo '<br>';
// echo 'Numero de no cargados: ' . $archivosNoCargadosCount;
// echo '<br>';
// echo '<br>';
// echo 'Archivos no validos';
// echo '<br>';
// print_r($archivosNoValidos);
// echo '<br>';
// echo 'Archivos no cargados';
// echo '<br>';
// print_r($archivosNoCargados);


// if ($xml->schemaValidate($xsdFile)) {
            //     // Obtén el valor del atributo currencyID
            //     $currencyID = $xpath->query('//cbc:LineExtensionAmount/@currencyID')->item(0)->nodeValue;

            //     // Obtén el contenido del tag LineExtensionAmount
            //     $lineExtensionAmount = $xpath->query('//cbc:LineExtensionAmount')->item(0)->nodeValue;
            //     echo 'Currency ID: ' . $currencyID . '<br>';
            //     echo 'Line Extension Amount: ' . $lineExtensionAmount . '<br>';
            //     echo 'Nombre XML: ' . $nombreArchivo . '<br>' . '<br>';
            // } else {
            //     // $lineExtensionAmount = $xpath->query('//cbc:LineExtensionAmount')->item(0)->nodeValue;
            //     // echo 'Line Extension Amount: ' . $lineExtensionAmount . '<br>';
            //     $archivosInvalidosCount++;
            //     $errores = libxml_get_errors();
            //     $mensajesErrores = "";
            //     foreach ($errores as $error) {
            //         $mensajesErrores = $error->message;
            //     }
            //     $archivosNoValidos[] = array('archivo' => $nombreArchivo, 'errores' => $mensajesErrores);
            // }
