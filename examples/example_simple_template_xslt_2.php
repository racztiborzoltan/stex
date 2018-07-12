<?php
use Stex\SimpleTemplateXslt;
use Stex\VariableList;

require_once '../vendor/autoload.php';

$stex = new SimpleTemplateXslt();

libxml_use_internal_errors(true);

$xsl = new \DOMDocument();
$xsl->load('collection.xsl');
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

$stex->setXmlDocument($variables->toDomDocument('collection'));

$dom_document = $stex->transformToDomDocument();

$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

echo $dom_document->saveHTML();
