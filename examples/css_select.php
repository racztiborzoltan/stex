<?php
use Stex\StexXsltProcessor;

require_once '../vendor/autoload.php';

$stex = new StexXsltProcessor();

$prev_internal_error = libxml_use_internal_errors(true);

$xsl = new \DOMDocument();
$xsl->load('css_select.xsl');
$stex->setXslDocument($xsl);
// or:
// $stex->importStylesheet($xsl);

$xml = new \DOMDocument();
$xml->load('collection.xml');

libxml_use_internal_errors($prev_internal_error);

$dom_document = $stex->transformToDoc($xml);


$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

header('Content-Type: text/plain');
echo $dom_document->saveHTML();
