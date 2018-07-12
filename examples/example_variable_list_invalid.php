<?php
use Stex\VariableList;

require_once '../vendor/autoload.php';

header('Content-Type: text/plain');

$variables = new VariableList();
$variables->set('1foo', 'bar');
$variables->set('var', 12345);

try {
    $dom_document = $variables->toDomDocument('variables');
} catch (\DOMException $e) {

    var_export($e);
    exit();

}


$dom_document->formatOutput = true;
echo $dom_document->saveXML($dom_document);
