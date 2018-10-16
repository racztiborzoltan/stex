<?php
use Stex\StexXsltProcessor;

//
// Original code: http://php.net/manual/en/xsltprocessor.registerphpfunctions.php
//

require_once '../../vendor/autoload.php';

$xmldoc = (new \DOMDocument());
$xmldoc->load('../example.xml');

$xsldoc = new \DOMDocument();
$xsldoc->load('simple_register_php_functions.xsl');

$proc = new StexXsltProcessor();
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsldoc);

header('Content-Type: text/plain');
echo $proc->transformToXML($xmldoc);
