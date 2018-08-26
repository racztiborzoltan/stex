<?php
declare(strict_types=1);

namespace Zita\Tests;

use PHPUnit\Framework\TestCase;
use League\Container\Container;
use Stex\StexXsltProcessor;

final class StexXsltProcessorContainerTest extends TestCase
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
        $container->add('foo', 'bar');
        $container->add('datetime', function(){
            return new \DateTime();
        });

        $proc = new StexXsltProcessor();

        $proc->setContainer($container);

        $xslDoc = new \DOMDocument();
        $xslDoc->load(__DIR__.'/StexXsltProcessor_test.xsl');

        $xmlDoc = new \DOMDocument();
        $xmlDoc->load(__DIR__.'/StexXsltProcessor_test.xml');

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

    public function test_transformToDoc()
    {
        $result_doc = $this->_getStexXsltProcessor()->transformToDoc($this->_getXmlDocument()->firstChild);
        $this->_testNodeValues($result_doc);
    }

    public function test_transformToUri()
    {
        $output_path = 'file://'.__DIR__.'/temp/test_transform_to_uri_output.xml';
        $this->_getStexXsltProcessor()->transformToUri($this->_getXmlDocument(), $output_path);

        $result = new \DOMDocument();
        $result->load($output_path);

        $this->_testNodeValues($result);
    }

    protected function _testNodeValues(\DOMDocument $dom_document)
    {
        $this->_testNodeValue($dom_document->getElementsByTagName('php_function'), 'FOOBAR');
        $this->_testNodeValue($dom_document->getElementsByTagName('foo'), 'bar');
        $this->_testNodeValue($dom_document->getElementsByTagName('datetime'), date('Y-m-d'));
    }

    protected function _testNodeValue(\DOMNodeList $node_list, $expected_value)
    {
        $this->assertTrue($node_list->length === 1);
        if ($node_list->length === 1) {
            $test_node = $node_list->item(0);
            $this->assertEquals($expected_value, $test_node->nodeValue);
        }
    }
}
