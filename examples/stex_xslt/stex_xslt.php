<?php
use Stex\StexXsltProcessor;

require_once '../../vendor/autoload.php';

$stex = new StexXsltProcessor();


$xsl = new \DOMDocument();
$xsl->load('stex_xslt.xsl');
$stex->setXslDocument($xsl);

$xml = new \DOMDocument();
$xml->load('../example.xml');

$dom_document = $stex->transformToDoc($xml);

$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

echo $dom_document->saveHTML();
