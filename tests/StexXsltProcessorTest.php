<?php
declare(strict_types=1);

namespace Stex\Tests;

use PHPUnit\Framework\TestCase;
use Stex\StexXsltProcessor;
use Stex\VariableList;

final class StexXsltProcessorTest extends TestCase
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

    /**
     * @var \DOMDocument
     */
    private $_variable_list_document = null;

    private $_test_message = 'test message';

    private $_raw_xsl_document = '
        <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
        	<xsl:output method="xml" encoding="utf-8" indent="yes" omit-xml-declaration="yes" />
            <xsl:strip-space elements="root,message"/>
        	<xsl:template match="/root/message">
                <xsl:element name="test">
                    <xsl:value-of select="." />
                </xsl:element>
        	</xsl:template>
        </xsl:stylesheet>
    ';

    private $_result_raw_string = '<test>test message</test>';

    public function setUp()
    {
        $proc = new StexXsltProcessor();

        $xslDoc = new \DOMDocument();
        $xslDoc->loadXML($this->_raw_xsl_document);

        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML('<root><message>'.$this->_test_message.'</message></root>');

        $proc->importStylesheet($xslDoc);

        $this->_xslt_processor = $proc;
        $this->_xml_document = $xmlDoc;
        $this->_xsl_document = $xslDoc;

        $variables = new VariableList();
        $variables->set('message', $this->_test_message);
        $this->_variable_list_document= $variables->toDomDocument('root');

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

    protected function _getVariableListDocument(): \DOMDocument
    {
        return $this->_variable_list_document;
    }

    public function test_withVariableList()
    {
        $stex = $this->_getStexXsltProcessor();
        $stex->setXmlDocument($this->_getVariableListDocument());

        $result_doc = new \DOMDocument();
        $result_doc->loadXML((string)$stex);

        $this->_assertTestMessage($result_doc, '/test', $this->_test_message);
    }

    public function test_toString()
    {
        $stex = $this->_getStexXsltProcessor();
        $stex->setXmlDocument($this->_getXmlDocument());

        $result_doc = new \DOMDocument();
        $result_doc->loadXML((string)$stex);

        $this->_assertTestMessage($result_doc, '/test', $this->_test_message);
    }

    public function test_render()
    {
        $stex = $this->_getStexXsltProcessor();
        $stex->setXmlDocument($this->_getXmlDocument());

        $result_doc = new \DOMDocument();
        $result_doc->loadXML($stex->render());

        $this->_assertTestMessage($result_doc, '/test', $this->_test_message);
    }

    public function test_transformToXml()
    {
        $stex = $this->_getStexXsltProcessor();

        $result = $stex->transformToXml($this->_getXmlDocument());

        $result_doc = $result;

        $result_doc = new \DOMDocument();
        $result_doc->loadXML($result);

        $this->_assertTestMessage($result_doc, '/test', $this->_test_message);
    }

    public function test_transformToDoc()
    {
        $stex = $this->_getStexXsltProcessor();

        $result = $stex->transformToDoc($this->_getXmlDocument());

        $result_doc = $result;

        $this->_assertTestMessage($result_doc, '/test', $this->_test_message);
    }

    public function test_transformToUri()
    {
        $stex = $this->_getStexXsltProcessor();

        $output_path = 'file://'.__DIR__.'/temp/StexXsltProcessor_test_transform_to_uri_output.xml';
        $result = $stex->transformToUri($this->_getXmlDocument(), $output_path);

        $result = new \DOMDocument();
        $result->load($output_path);

        $this->_assertTestMessage($result, '/test', $this->_test_message);
    }

    private function _assertTestMessage(\DOMDocument $dom_document, string $xpath_expression, $expected_value)
    {
        $xpath = new \DOMXPath($dom_document);
        $nodes = $xpath->query($xpath_expression);
        if ($nodes->length !== 1) {
            $this->assertFalse($nodes->length !== 1, 'test node is not found');
        } else {
            $node = $nodes->item(0);
            $this->assertEquals($node->nodeValue, $this->_test_message);
        }
    }
}