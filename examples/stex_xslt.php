<?php
use Stex\StexXsltProcessor;

require_once '../vendor/autoload.php';

$stex = new StexXsltProcessor();

libxml_use_internal_errors(true);

$xsl = new \DOMDocument();
$xsl->load('collection.xsl');
$stex->setXslDocument($xsl);

$xml = new \DOMDocument();
$xml->load('collection.xml');

$dom_document = $stex->transformToDoc($xml);

$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

echo $dom_document->saveHTML();
