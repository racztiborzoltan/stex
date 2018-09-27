<?php
declare(strict_types=1);

namespace Zita\Tests;

use PHPUnit\Framework\TestCase;
use League\Container\Container;
use Stex\StexXsltProcessor;

final class StexCssSelectTest extends TestCase
{

    /**
     * @var StexXsltProcessor
     */
    private $_xslt_processor = null;

    /**
     * @var \DOMDocument
     */
    private $_xml_document = null;

    /**
     * @var \DOMDocument
     */
    private $_xsl_document = null;

    public function setUp()
    {
        $container = new Container();

        $proc = new StexXsltProcessor();

        $proc->setContainer($container);

        $xslDoc = new \DOMDocument();
        $xslDoc->load(__DIR__.'/files/'.preg_replace('#.*\\\#', '', __CLASS__).'.xsl');

        $xmlDoc = new \DOMDocument();
        $xmlDoc->load(__DIR__.'/files/'.preg_replace('#.*\\\#', '', __CLASS__).'.xml');

        $proc->importStylesheet($xslDoc);

        $this->_xslt_processor = $proc;
        $this->_xml_document = $xmlDoc;
        $this->_xsl_document = $xslDoc;
    }

    protected function _getStexXsltProcessor(): StexXsltProcessor
    {
        return $this->_xslt_processor;
    }

    protected function _getXmlDocument(): \DOMDocument
    {
        return $this->_xml_document;
    }

    protected function _getXslDocument(): \DOMDocument
    {
        return $this->_xsl_document;
    }

    public function test_transformToXml()
    {
        $result_xml = $this->_getStexXsltProcessor()->transformToXml($this->_getXmlDocument());

        $result = new \DOMDocument();
        $result->loadXML($result_xml);

        $this->_testNodeValues($result);
    }

    protected function _testNodeValues(\DOMDocument $dom_document)
    {
        $this->_testNodeValue($dom_document->getElementsByTagName('test'));
    }

    protected function _testNodeValue(\DOMNodeList $node_list)
    {
        foreach ($node_list as $node) {
            /**
             * @var \DOMElement $node
             */
            $expected_value = $node->getAttribute('expected-value');
            $this->assertEquals($expected_value, $node->nodeValue);
        }
    }
}
