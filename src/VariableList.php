<?php
namespace Stex;

class VariableList
{

    /**
     * Variable list
     *
     * @var array
     */
    private $_variables = [];

    /**
     * Set variable
     *
     * @param string $name
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return \Stex\VariableList
     */
    public function set($name, $value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            throw new \InvalidArgumentException('invalid data type in second parameter. Valid data types: integer, float, string, boolean or array');
        }
        $this->_variables[$name] = $value;
        return $this;
    }

    /**
     * Check variable exists
     *
     * @param string $name
     * @return boolean
     */
    public function isset($name)
    {
        return isset($this->_variables[$name]);
    }

    /**
     * Check variable exists
     *
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        return $this->isset($name);
    }

    /**
     * Remove variable
     *
     * @param string $name
     */
    public function unset($name)
    {
        unset($this->_variables[$name]);
        return $this;
    }

    /**
     * Remove variable
     *
     * @param string $name
     */
    public function remove($name)
    {
        $this->unset($name);
        return $this;
    }

    /**
     * Returns variable value
     *
     * @param string $name
     * @param mixed $default_value return value if first parameter not exists
     * @return mixed
     */
    public function get($name, $default_value = null)
    {
        return $this->isset($name) ? $this->_variables[$name] : $default_value;
    }

    /**
     * Overwrite the entire variable list
     *
     * @param array $variables
     * @return \Stex\VariableList
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $name => $value) {
            $this->set($name, $value);
        }
        $this->_variables = $variables;
        return $this;
    }

    /**
     * Convert variable list to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->_variables;
    }

    /**
     * Convert variable list to JSON string
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert variable list to DOMDocument object
     *
     * @return \DOMDocument
     */
    public function toDomDocument(string $variables_node_name): \DOMDocument
    {
        $variables = $this->toArray();

        $dom_document = (new \DOMImplementation)->createDocument();

        $root_node = $dom_document->createElement($variables_node_name);
        $dom_document->appendChild($root_node);

        $this->_createNodes($root_node, $variables);

        return $dom_document;
    }

    /**
     * Create nodes recursively in DOMElement by array items
     *
     * @param \DOMElement $parent_element
     * @param array $items
     * @throws \DOMException
     */
    private function _createNodes(\DOMElement $parent_element, array $items)
    {
        foreach ($items as $node_name => $node_value) {
            // ignore if not scalar or not array:
            if (!is_scalar($node_value) && !is_array($node_value)) {
                continue;
            }

            // create base node object:
            try {
                $node = $parent_element->ownerDocument->createElement(is_numeric($node_name) ? $parent_element->nodeName : $node_name);
            } catch (\DOMException $e) {
                throw new \DOMException('Invalid XML tag name: '.$node_name, $e->getCode(), $e);
            }

            // append to parent element:
            $parent_element->appendChild($node);

            if (is_scalar($node_value)) {

                if (is_bool($node_value)) {
                    $node_value = $node_value ? 'true' : 'false';
                }
                // create CDATA subnode if node value is an scalar:
                $node->appendChild($parent_element->ownerDocument->createCDATASection($node_value));

            } elseif (is_array($node_value)) {

                // recursive call if node value is array
                $this->_createNodes($node, $node_value);
                // if numeric indexed array:
                if ($node->textContent == '' && !empty($node_value)) {
                    $node->parentNode->removeChild($node);
                }

            }
            if (is_numeric($node_name)) {
                $parent_element->parentNode->appendChild($node);
            }
        }
    }
}