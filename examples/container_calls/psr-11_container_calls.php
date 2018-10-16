<?php
use Stex\StexXsltProcessor;
use League\Container\Container;

//
// Original code: http://php.net/manual/en/xsltprocessor.registerphpfunctions.php
//

require_once '../../vendor/autoload.php';

$xmldoc = new \DOMDocument();
$xmldoc->load('../example.xml');

$xsldoc = new \DOMDocument();
$xsldoc->load('container_calls.xsl');

// define container and container items
$container = new Container();
$container->add('foo', 'bar');
$container->add('datetime', function(){
    return new \DateTime();
});

$proc = new StexXsltProcessor();
$proc->setContainer($container);
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsldoc);
echo $proc->transformToXML($xmldoc);
