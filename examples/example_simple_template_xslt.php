<?php
use Stex\SimpleTemplateXslt;

require_once '../vendor/autoload.php';

$stex = new SimpleTemplateXslt();

libxml_use_internal_errors(true);

$xsl = new \DOMDocument();
$xsl->load('collection.xsl');
$stex->setXslDocument($xsl);

$xml = new \DOMDocument();
$xml->load('collection.xml');
$stex->setXmlDocument($xml);

$dom_document = $stex->transformToDomDocument();

$dom_document->formatOutput = true;
$dom_document->preserveWhiteSpace = false;
$dom_document->recover = true;
$dom_document->encoding = 'UTF-8';

echo $dom_document->saveHTML();
