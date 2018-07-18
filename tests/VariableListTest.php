<?php
declare(strict_types=1);

namespace DomOperationQueue\Tests;

use PHPUnit\Framework\TestCase;
use Stex\VariableList;

final class VariableListTest extends TestCase
{

    private $_root_node_name = 'variables';

    private $variable_array = [];

    public function setUp()
    {
        $this->variable_array = [
            'foo' => 'bar',
            'var' => 7873534,
            'string' => 'Lorem Ipsum',
            'integer' => 123456,
            'float' => 12345.67,
            'boolean_true' => true,
            'boolean_false' => false,
            'html_content' => '<h3>asgagd</h3><script type="adgs">adga();</script>',
            'multilevel' => [
                'level1.1' => 'value 1.1',
                'level1.2' => [
                    'level2.1' => 'value 2.1',
                    'level2.2' => 'value 2.2',
                ],
                'level1.3' => 'value 1.3',
                'level1.4' => new \stdClass(),
            ],
            'numberic_indexed_array' => [
                'numeric_indexed_value_1',
                'numeric_indexed_value_2',
                'numeric_indexed_value_3',
            ],
        ];
    }

    public function getRootNodeName(): string
    {
        return $this->_root_node_name;
    }

    public function setRootNodeName(string $root_node_name)
    {
        $this->_root_node_name = $root_node_name;
        return $this;
    }

    /**
     *
     * @return \DOMDocument
     */
    protected function _factoryVariableList(): VariableList
    {
        $variables = new VariableList();
        foreach ($this->variable_array as $key => $value) {
            $variables->set($key, $value);
        }
        return $variables;
    }

    public function testVariableList()
    {
        $variables = $this->_factoryVariableList();
        $dom_document = $variables->toDomDocument($this->getRootNodeName());

        $this->_checkDomDocumentWithXpath($dom_document, '/'.$this->getRootNodeName(), $this->variable_array);
    }

    public function testInvalidVariableList()
    {
        $variables = $this->_factoryVariableList();

        // set an invalid node name:
        $variables->set('1foo', 'bar');

        try {
            $variables->toDomDocument($this->getRootNodeName());
            $this->assertFalse(false);
        } catch (\DOMException $e) {
            $this->assertTrue(true);
        }
    }

    private function _checkDomDocumentWithXpath(\DOMDocument $dom_document, string $xpath_prefix, array $variables_array)
    {
        foreach ($variables_array as $name => $value) {
            if (is_numeric($name)) {
                $xpath_expression = $xpath_prefix.'['.($name+1).']';
            } else {
                $xpath_expression = $xpath_prefix.'/'.$name;
            }

            if (is_array($value)) {
                $this->_checkDomDocumentWithXpath($dom_document, $xpath_expression, $value);
            } else {
                $result = $this->_xpathTest($dom_document, $xpath_expression, $value);
                $this->assertTrue($result);
            }
        }
    }

    private function _xpathTest(\DOMDocument $dom_document, string $xpath_expression, $expected_node_value): bool
    {
        $xpath = new \DOMXPath($dom_document);
        $nodes = $xpath->query($xpath_expression);

        // If object and if node list is empty, than this is correct:
        if (is_object($expected_node_value) && $nodes->length === 0) {
            return true;
        }

        if ($nodes->length === 0) {
            return false;
        }
        $node = $nodes->item(0);

        if (is_bool($expected_node_value)) {
            $expected_node_value = $expected_node_value === true ? 'true' : 'false';
        }

        return $node->nodeValue == $expected_node_value;
    }
}