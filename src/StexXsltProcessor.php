<?php
namespace Stex;

use Psr\Container\ContainerInterface;

/**
 * Stex XSLT Processor with PSR-11 (ContainerInterface) support
 *
 * This extended class supports the following XSLT attribute syntax:
 *  select="this:container('CONTAINER_ITEM_NAME')"
 *  select="this:container('CONTAINER_ITEM_NAME', 'FIRST_PARAMETER', 'SECOND_PARAMETER', '...')"
 *
 * 'this:container' point always to ContainerInterace object in the current instance
 * (with some magic)
 *
 * @author Rácz Tibor Zoltán <racztiborzoltan@gmail.com>
 *
 */
class StexXsltProcessor extends SimpleTemplateXsltProcessor
{

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
     * Operations before xslt transformation
     */
    protected function _beforeTransform()
    {
        parent::_beforeTransform();

        $alias_class_name = static::getStaticContainerCallsClassName() . '_' . str_replace('.', '', uniqid(null, true));
        // set StaticContainerClass class alias:
        class_alias(static::getStaticContainerCallsClassName(), $alias_class_name);

        /**
         * @var StaticContainerCalls $alias_class_name
         */
        $alias_class_name::setContainer($this->getContainer());

        // prepare the original xsl document:
        $xsl_document = $this->getXslDocument();
        $xsl_document = $this->_prepareXslDocument($xsl_document, $alias_class_name);
        parent::importStylesheet($xsl_document);
    }

    /**
     * Operations after xslt transformation
     */
    protected function _afterTransform()
    {
        parent::_afterTransform();

        // remove class alias is not possible :(

    }

    /**
     * @param \DOMDocument $xsl_document
     * @param string $static_container_calls_alias
     * @return \DOMDocument
     */
    protected function _prepareXslDocument(\DOMDocument $xsl_document, string $static_container_calls_alias): \DOMDocument
    {

        $xpath = new \DOMXPath($xsl_document);
        $php_function_calls = $xpath->query('//@select[starts-with(normalize-space(.), "php:function")]');
        if ($php_function_calls->length > 0) {
            $this->registerPHPFunctions();
        }

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

        return $xsl_document;
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
}
