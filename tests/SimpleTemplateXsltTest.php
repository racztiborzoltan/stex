<?php
declare(strict_types=1);

namespace DomOperationQueue\Tests;

use PHPUnit\Framework\TestCase;
use Stex\SimpleTemplateXslt;
use Stex\VariableList;

final class SimpleTemplateXsltTest extends TestCase
{

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

    protected function _factorySimpleTemplateXslt(): SimpleTemplateXslt
    {
        $stex = new SimpleTemplateXslt();

        $xsl = new \DOMDocument();
        $xsl->loadXML($this->_raw_xsl_document);
        $stex->setXslDocument($xsl);

        $xml = new \DOMDocument();
        $xml->loadXML('<root><message>'.$this->_test_message.'</message></root>');
        $stex->setXmlDocument($xml);

        return $stex;
    }

    protected function _factorySimpleTemplateXsltWithVariableList(): SimpleTemplateXslt
    {
        $stex = new SimpleTemplateXslt();

        $xsl = new \DOMDocument();
        $xsl->loadXML($this->_raw_xsl_document);
        $stex->setXslDocument($xsl);

        $variables = new VariableList();
        $variables->set('message', $this->_test_message);
        $xml_document = $variables->toDomDocument('root');
        $stex->setXmlDocument($xml_document);

        return $stex;
    }

    public function test_transformToDomDocument()
    {
        $this->_assertTestMessage($this->_factorySimpleTemplateXslt()->transformToDomDocument(), '/test', $this->_test_message);
        $this->_assertTestMessage($this->_factorySimpleTemplateXsltWithVariableList()->transformToDomDocument(), '/test', $this->_test_message);
    }

    public function test_renderToDomDocument()
    {
        $this->_assertTestMessage($this->_factorySimpleTemplateXslt()->renderToDomDocument(), '/test', $this->_test_message);
        $this->_assertTestMessage($this->_factorySimpleTemplateXsltWithVariableList()->renderToDomDocument(), '/test', $this->_test_message);
    }

    public function test_render()
    {
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXslt()->render()));
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXsltWithVariableList()->render()));
    }

    public function test_renderToString()
    {
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXslt()->renderToString()));
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXsltWithVariableList()->renderToString()));
    }

    public function test_transformToString()
    {
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXslt()->transformToString()));
        $this->assertEquals($this->_result_raw_string, trim($this->_factorySimpleTemplateXsltWithVariableList()->transformToString()));
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