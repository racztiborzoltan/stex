<?php
namespace Stex;

/**
 * XSLT sablonok
 *
 * @author Rácz Tibor Zoltán <racztiborzoltan@gmail.com>
 *
 */
class SimpleTemplateXslt
{

    /**
     * @var \DOMDocument
     */
    private $_xsl_document = null;

    /**
     * @var \DOMDocument
     */
    private $_xml_document = null;

    /**
     * @var \XSLTProcessor
     */
    private $_xslt_processor = null;

    /**
     * Set XSL document object for XSLT transformation
     *
     * @param \DOMDocument $xsl_document
     * @return \Stex\SimpleTemplateXslt
     */
    public function setXslDocument(\DOMDocument $xsl_document)
    {
        $this->_xsl_document = $xsl_document;
        return $this;
    }

    /**
     * Returns XSL document object for XSLT transformation
     *
     * @return \DOMDocument
     */
    public function getXslDocument()
    {
        return $this->_xsl_document;
    }

    /**
     * Set XML document object for XSLT transformation
     *
     * @param \DOMDocument $xml_document
     * @return \Stex\SimpleTemplateXslt
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
        return $this->_xml_document;
    }

    /**
     * Returns XSLTProcessor object for XSLT transformation
     *
     * @return \XSLTProcessor
     */
    public function getXsltProcessor()
    {
        if (empty($this->_xslt_processor)) {
            $this->_xslt_processor = new \XSLTProcessor();
        }
        return $this->_xslt_processor;
    }

    /**
     * Set XSLTProcessor object for XSLT transformation
     *
     * @param \DOMDocument $xslt_processor
     * @return \Stex\SimpleTemplateXslt
     */
    public function setXsltProcessor(\XSLTProcessor $xslt_processor)
    {
        $this->_xslt_processor = $xslt_processor;
        return $this;
    }

    /**
     * XSLT transformation to DOMDocument object
     *
     * @return \DOMDocument
     */
    public function transformToDomDocument(): \DOMDocument
    {
        $this->getXsltProcessor()->importStylesheet($this->getXslDocument());
        return $this->getXsltProcessor()->transformToDoc($this->getXmlDocument());
    }

    /**
     * XSLT transformation into string
     *
     * @return string
     */
    public function transformToString(): string
    {
        $dom_document = $this->renderDomDocument();
        return $dom_document->saveHTML($dom_document);
    }

    /**
     * Render XSLT template into string
     *
     * @return string
     */
    public function render(): string
    {
        return $this->transformToString();
    }

    /**
     * Render XSLT template into string
     *
     * @return string
     */
    public function renderToString(): string
    {
        return $this->transformToString();
    }

    /**
     * Render XSLT template into DOMDocument object
     *
     * @return string
     */
    public function renderToDomDocument(): \DOMDocument
    {
        return $this->transformToDomDocument();
    }
}