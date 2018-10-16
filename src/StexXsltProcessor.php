<?php
namespace Stex;

use Psr\Container\ContainerInterface;

/**
 * Stex XSLT Processor with PSR-11 (ContainerInterface) support
 *
 * This extended class supports the following XSLT function syntax:
 *  this:container('CONTAINER_SCALAR_ITEM_NAME')
 *  this:container('CONTAINER_FUNCTION_ITEM_NAME', 'PARAMETER_1', 'PARAMETER_2', '...')
 *  this:container('CONTAINER_OBJECT_ITEM_NAME', 'METHOD_NAME', 'FIRST_PARAMETER', 'SECOND_PARAMETER', '...')
 *
 * Usage example:
 *  <xsl:value-of select="this:container('scalar_item_name')/>
 *  <xsl:value-of select="this:container('function_item_name', 'foobar', 123)/>
 *  <xsl:value-of select="this:container('object_item_name', 'method_name', 'arg_1', 'arg2')/>
 *
 * 'this:container' point always to ContainerInterace object in the current instance
 * (with some magic)
 *
 * @author Rácz Tibor Zoltán <racztiborzoltan@gmail.com>
 *
 */
class StexXsltProcessor extends \XSLTProcessor
{

    /**
     * XSL(T) \DOMDocument object
     *
     * @var \DOMDocument
     */
    private $_xsl_document = null;

    /**
     * XML \DOMDocument object
     *
     * @var \DOMDocument
     */
    private $_xml_document = null;

    /**
     * container object
     *
     * @var ContainerInterface
     */
    private $_container = null;

    /**
     * Class name of StaticContainerCalls class
     *
     * @var string
     */
    private $_static_container_calls_class_name = null;

    /**
     * Set XSL document object for XSLT transformation
     *
     * @param \DOMDocument $xsl_document
     * @return self
     */
    public function setXslDocument(\DOMDocument $xsl_document)
    {
        $this->_xsl_document = $xsl_document;
        $this->importStylesheet($xsl_document);
        return $this;
    }

    /**
     * Returns XSL document object for XSLT transformation
     *
     * @return \DOMDocument
     */
    public function getXslDocument()
    {
        if (empty($this->_xsl_document)) {
            throw new \LogicException('XSL document is not exists. Use before the following methods: ->setXslDocument()');
        }
        return $this->_xsl_document;
    }

    /**
     * Set XML document object for XSLT transformation
     *
     * @param \DOMDocument $xml_document
     * @return self
     */
    public function setXmlDocument(\DOMDocument $xml_document)
    {
        $this->_xml_document = $xml_document;
        return $this;
    }

    /**
     * Returns XML document object for XSLT transformation
     *
     * @return \DOMDocument
     */
    public function getXmlDocument()
    {
        if (empty($this->_xml_document)) {
            throw new \LogicException('XML document object is not set. Use before the following method: ->setXmlDocument()');
        }
        return $this->_xml_document;
    }

    /**
     * Set container object
     *
     * @param ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Set container object
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->_container;
    }

    public function getStaticContainerCallsClassName()
    {
        if (empty($this->_static_container_calls_class_name)) {
            return StaticContainerCalls::class;
        } else {
            return $this->_static_container_calls_class_name;
        }
    }

    public function setStaticContainerCallsClassName(string $class_name)
    {
        $this->_static_container_calls_class_name = $class_name;
    }

    /**
     * Imported XSL \DOMDocument object
     *
     * @var \DOMDocument
     */
    private $_imported_stylesheet = null;

    /**
     * {@inheritDoc}
     * @see \XSLTProcessor::importStylesheet()
     */
    public function importStylesheet($stylesheet)
    {
        if ($stylesheet instanceof \SimpleXMLElement) {
            $xsl_document = dom_import_simplexml($stylesheet);
            $xsl_document->ownerDocument;
        } elseif ($stylesheet instanceof \DOMDocument) {
            $xsl_document = $stylesheet;
        }
        if (isset($xsl_document)) {

            $this->_prepareStexCssSelectAttribute($xsl_document);

            try {
                if ($this->getXslDocument() !== $xsl_document) {
                    throw new \LogicException();
                }
            } catch (\LogicException $e) {
                $this->setXslDocument($xsl_document);
            }
            $this->_imported_stylesheet = $xsl_document;
        }
        return parent::importStylesheet($stylesheet);
    }

    /**
     * {@inheritDoc}
     * @see \XSLTProcessor::transformToDoc()
     * @return \DOMDocument
     */
    public function transformToDoc($doc)
    {
        if ($doc instanceof \DOMNode) {
            if ($doc instanceof \DOMDocument) {
                $this->setXmlDocument($doc);
            } else {
                $this->setXmlDocument($doc->ownerDocument);
            }
        }
        $this->_startBeforeTransform();
        $result = parent::transformToDoc($doc);
        $this->_startAfterTransform();
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \XSLTProcessor::transformToUri()
     * @return int
     */
    public function transformToUri($doc, $uri)
    {
        if ($doc instanceof \DOMDocument) {
            $this->setXmlDocument($doc);
        }
        $this->_startBeforeTransform();
        $result = parent::transformToUri($doc, $uri);
        $this->_startAfterTransform();
        return $result;
    }

    /**
     * {@inheritDoc}
     * @see \XSLTProcessor::transformToXml()
     * @return string
     */
    public function transformToXml($doc)
    {
        if ($doc instanceof \SimpleXMLElement) {
            $xml_document = dom_import_simplexml($doc);
            $xml_document->ownerDocument;
        } elseif ($doc instanceof \DOMDocument) {
            $xml_document = $doc;
        }
        if (isset($xml_document)) {
            $this->setxmlDocument($xml_document);
        }

        $this->_startBeforeTransform();
        $result = parent::transformToXml($doc);
        $this->_startAfterTransform();
        return $result;
    }

    /**
     * Operations before xslt transformation
     */
    public function beforeTransform()
    {

    }

    /**
     * Operations after xslt transformation
     */
    public function afterTransform()
    {

    }

    /**
     * Start operations before xslt transformation
     */
    protected function _startBeforeTransform()
    {
        $alias_class_name = static::getStaticContainerCallsClassName() . '_' . str_replace('.', '', uniqid(null, true));
        // set StaticContainerClass class alias:
        class_alias(static::getStaticContainerCallsClassName(), $alias_class_name);

        /**
         * @var StaticContainerCalls $alias_class_name
         */
        try {
            $alias_class_name::setContainer($this->getContainer());
        } catch (\TypeError $e) {
        }

        // prepare the original xsl document:
        $xsl_document = $this->getXslDocument();
        $xsl_document = $this->_prepareXslDocument($xsl_document, $alias_class_name);

        parent::importStylesheet($xsl_document);

        $this->beforeTransform();
    }

    /**
     * Start operations after xslt transformation
     */
    protected function _startAfterTransform()
    {
        // remove class alias is not possible :(

        $this->afterTransform();
    }

    /**
     * @param \DOMDocument $xsl_document
     * @param string $static_container_calls_alias
     * @return \DOMDocument
     */
    protected function _prepareXslDocument(\DOMDocument $xsl_document, string $static_container_calls_alias): \DOMDocument
    {
        $this->_repairRegisterPHPFunctions($xsl_document);
        $this->_prepareContainerCalls($xsl_document, $static_container_calls_alias);
        return $xsl_document;
    }

    protected function _repairRegisterPHPFunctions(\DOMDocument $xsl_document)
    {
        $xpath = new \DOMXPath($xsl_document);
        $php_function_calls = $xpath->query('//@select[starts-with(normalize-space(.), "php:function")]');
        if ($php_function_calls->length > 0) {
            $this->registerPHPFunctions();
        }
    }

    protected function _prepareContainerCalls(\DOMDocument $xsl_document, string $static_container_calls_alias)
    {
        $xpath = new \DOMXPath($xsl_document);
        $container_calls = $xpath->query('//@select[starts-with(normalize-space(.), "this:container(")]');
        if ($container_calls->length === 0) {
            return $xsl_document;
        }

        /**
         * @var StaticContainerCalls $static_container_calls_alias
         */
        for ($i = 0; $i < $container_calls->length; $i++) {
            $call_node = $container_calls->item($i);
            $attribute_value = $call_node->nodeValue;
            $attribute_value = str_replace('this:container(', 'php:function(\'' . $static_container_calls_alias.'::call\', ', $attribute_value);
            $call_node->nodeValue = $attribute_value;
        }

        if ($container_calls->length > 0) {
            $this->registerPHPFunctions();

            $root_node = $xsl_document->documentElement;

            $xml_php_namespace = $root_node->getAttributeNode('xmlns:php');
            if (!$xml_php_namespace) {
                $root_node->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:php', 'http://php.net/xsl');
            }

            // fix "exclude-result-prefixes" attribute ...
            $this->_fixExcludeResultPrefixesAttribute($xsl_document);

        }
    }

    private const CSS_SELECTOR_EXPRESSION_BEGIN = '@css[';
    private const CSS_SELECTOR_EXPRESSION_END = ']';

    /**
     * Convert CSS expression ("@css[__CSS_SELECTOR__]") to Xpath expression
     *
     * Returns null if an error has occurred. (invalid css expression or invalid css selector)
     *
     * @param string $css_expression
     * @param string $xpath_prefix
     * @return string|NULL
     */
    protected function _convertCssExpressionToXpath($css_expression, $xpath_prefix = 'descendant-or-self::'): ?string
    {
        if (substr($css_expression, 0, strlen(static::CSS_SELECTOR_EXPRESSION_BEGIN)) !== static::CSS_SELECTOR_EXPRESSION_BEGIN
            ||
            substr($css_expression, -1 * strlen(static::CSS_SELECTOR_EXPRESSION_END)) !== static::CSS_SELECTOR_EXPRESSION_END
        ) {
            return null;
        }
        $css_selector = substr($css_expression, strlen(static::CSS_SELECTOR_EXPRESSION_BEGIN), -1 * strlen(static::CSS_SELECTOR_EXPRESSION_END));
        $css_selector = trim($css_selector);

        if ($css_selector[0] === '\'' && $css_selector[strlen($css_selector)-1] === '\'') {
            $css_selector = trim($css_selector, '\'');
        }
        if ($css_selector[0] === '"' && $css_selector[strlen($css_selector)-1] === '"') {
            $css_selector = trim($css_selector, '"');
        }
        try {
            return (new \Stex\CssSelector\CssSelectorConverter())->toXPath($css_selector, $xpath_prefix);
        } catch (\Symfony\Component\CssSelector\Exception\SyntaxErrorException $e) {
            return null;
        }
    }

    protected function _prepareStexCssSelectAttribute(\DOMDocument $xsl_document)
    {
        $xpath = new \DOMXPath($xsl_document);
        $nodes = $xpath->query('//@select|//@match');
        foreach ($nodes as $select_attribute) {
            /**
             * @var \DOMAttr $select_attribute
             */
            while (($css_expression_start_pos = strpos($select_attribute->value, static::CSS_SELECTOR_EXPRESSION_BEGIN)) !== false) {
                $select_value = $select_attribute->value;
                if ($css_expression_start_pos === false) {
                    break;
                }
                $pos = $css_expression_start_pos + strlen(static::CSS_SELECTOR_EXPRESSION_BEGIN) - 1;
                $brackets = 1;
                $css_expression = static::CSS_SELECTOR_EXPRESSION_BEGIN;
                while ($brackets !== 0 && $pos !== strlen($select_value)-1 ) {
                    $pos++;
                    if ($select_value[$pos] == '[') {
                        $brackets ++;
                    }
                    if ($select_value[$pos] == ']') {
                        $brackets --;
                    }
                    $css_expression .= $select_value[$pos];
                }
                if ($brackets !== 0) {
                    continue;
                }

                if ($select_attribute->name == 'match') {
                    $converted_xpath_expression = $this->_convertCssExpressionToXpath($css_expression, '');
                } else {
                    $converted_xpath_expression = $this->_convertCssExpressionToXpath($css_expression);
                }
                if (is_null($converted_xpath_expression)) {
                    throw new \LogicException('stex: invalid css expression: ' . $css_expression);
                }

                $select_value = str_replace($css_expression, $converted_xpath_expression, $select_value);
                $select_attribute->value = $select_value;
            }
        }
    }

    /**
     * Fix the "exclude-result-prefixes" attribute in root node of XSL document
     *
     * @param \DOMDocument $xsl_document
     * @return void
     */
    protected function _fixExcludeResultPrefixesAttribute(\DOMDocument $xsl_document): void
    {
        $root_node = $xsl_document->documentElement;

        if (!in_array($root_node->localName, ['stylesheet', 'transform'])) {
            return;
        }

        // find "exclude-result-prefixes" attribute ...
        $exclude_result_prefixes_attr_name = 'exclude-result-prefixes';
        $exclude_result_prefixes_attr = $root_node->getAttributeNode($exclude_result_prefixes_attr_name);
        if (!$exclude_result_prefixes_attr) {
            // ... if not exists than create:
            $root_node->setAttribute($exclude_result_prefixes_attr_name, 'php');
        } else {
            // .. if exists than add "php" value:
            $exclude_result_prefixes_value = $exclude_result_prefixes_attr->nodeValue;
            $exclude_result_prefixes_value = explode(' ', $exclude_result_prefixes_value);
            $exclude_result_prefixes_value = array_filter($exclude_result_prefixes_value);
            if (!in_array('php', $exclude_result_prefixes_value)) {
                $exclude_result_prefixes_value[] = 'php';
            }
            $exclude_result_prefixes_value = implode(' ', $exclude_result_prefixes_value);
            $root_node->setAttribute($exclude_result_prefixes_attr_name, $exclude_result_prefixes_value);
        }
        $root_node->setAttribute('exclude-result-prefixes', 'php');
    }

    /**
     * Magic method for class to string conversion
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return (string)$e;
        }
    }

    /**
     * Render XSLT template into string
     *
     * @return string
     */
    public function render(): string
    {
        return $this->transformToXml($this->getXmlDocument());
    }
}
