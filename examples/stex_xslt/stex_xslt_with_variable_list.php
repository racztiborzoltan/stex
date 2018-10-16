<?php
use Stex\StexXsltProcessor;
use Stex\VariableList;

require_once '../../vendor/autoload.php';

$stex = new StexXsltProcessor();

$xsl = new \DOMDocument();
$xsl->load('stex_xslt.xsl');
$stex->setXslDocument($xsl);

$variables = new VariableList();
$variables->set('cd', [
    [
        'title' => 'Fight for your mind',
        'artist' => 'Ben Harper',
        'year' => 1995,
    ],
    [
        'title' => 'Electric Ladyland',
        'artist' => 'Jimi Hendrix',
        'year' => 1997,
    ],
]);

$dom_document = $stex->transformToDoc($variables->toDomDocument('collection'));

$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

echo $dom_document->saveHTML();
