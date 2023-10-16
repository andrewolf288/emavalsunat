<?php
// Ruta al archivo XML que deseas procesar
$directorio = 'comprobantes/septiembre';

// Ruta al archivo XSD que se utilizará para validar el XML
$xsdFile = 'xsd/UBL-Invoice-2.1.xsd';
$archivosNoCargados = [];

foreach (glob($directorio . '/*.[xX][mM][lL]', GLOB_BRACE) as $archivoXML) {
    // extraigo el nombre del archivo
    $nombreArchivo = pathinfo($archivoXML, PATHINFO_BASENAME);
    if (filesize($archivoXML) > 0) {
        $xml = simplexml_load_file($archivoXML);
        if ($xml !== false) {
        } else {
            $lastError = libxml_get_last_error();
            $archivosNoCargados[] = [
                'archivo' => $nombreArchivo,
                'error' => $lastError->message
            ];
        }
    } else {
        $archivosNoCargados[] = [
            'archivo' => $nombreArchivo,
            'error' => "El archivo esta vacio"
        ];
    }
}

print_r($archivosNoCargados);
