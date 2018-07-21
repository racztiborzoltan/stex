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
        	<xsl:output method="xml" encoding="utf-8" indent="yes" />
            <xsl:strip-space elements="root,message"/>
        	<xsl:template match="/root/message">
                <xsl:element name="test">
                    <xsl:value-of select="." />
                </xsl:element>
        	</xsl:template>
        </xsl:stylesheet>
    ';

    private $_result_raw_string = '<test>test message</test>';

    public function testSimpleTemplateXslt()
    {

        $stex = new SimpleTemplateXslt();

        $xsl = new \DOMDocument();
        $xsl->loadXML($this->_raw_xsl_document);
        $stex->setXslDocument($xsl);

        $xml = new \DOMDocument();
        $xml->loadXML('<root><message>'.$this->_test_message.'</message></root>');
        $stex->setXmlDocument($xml);

        /**
         * @var \DOMDocument $dom_document
         */
        $dom_document = $stex->transformToDomDocument();
        $this->_assertTestMessage($dom_document, '/test', $this->_test_message);
        unset($dom_document);

        $dom_document = $stex->renderToDomDocument();
        $this->_assertTestMessage($dom_document, '/test', $this->_test_message);
        unset($dom_document);

        $this->assertEquals($this->_result_raw_string, trim($stex->render()));
        $this->assertEquals($this->_result_raw_string, trim($stex->renderToString()));
        $this->assertEquals($this->_result_raw_string, trim($stex->transformToString()));
    }

    public function testSimpleTemplateXsltWithVariableList()
    {

        $stex = new SimpleTemplateXslt();

        $xsl = new \DOMDocument();
        $xsl->loadXML($this->_raw_xsl_document);
        $stex->setXslDocument($xsl);

        $variables = new VariableList();
        $variables->set('message', $this->_test_message);
        $xml_document = $variables->toDomDocument('root');
        $stex->setXmlDocument($xml_document);

        /**
         * @var \DOMDocument $dom_document
         */
        $dom_document = $stex->transformToDomDocument();

        $this->_assertTestMessage($dom_document, '/test', $this->_test_message);
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