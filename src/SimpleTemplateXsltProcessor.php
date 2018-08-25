<?php
namespace Stex;

/**
 * XSLT sablonok
 *
 * @author Rácz Tibor Zoltán <racztiborzoltan@gmail.com>
 *
 */
class SimpleTemplateXsltProcessor extends \XSLTProcessor
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
        if (empty($this->_xsl_document)) {
            throw new \LogicException('XSL document is not exists. Use before the following methods: ->setXslDocument()');
        }
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
        if (empty($this->_xml_document)) {
            throw new \LogicException('XML document object is not set. Use before the following method: ->setXmlDocument()');
        }
        return $this->_xml_document;
    }

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
            $this->setXslDocument($xsl_document);
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
        $this->_beforeTransform();
        $result = parent::transformToDoc($doc);
        $this->_afterTransform();
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
        $this->_beforeTransform();
        $result = parent::transformToUri($doc, $uri);
        $this->_afterTransform();
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

        $this->_beforeTransform();
        $result = parent::transformToXml($doc);
        $this->_afterTransform();
        return $result;
    }

    /**
     * Operations before xslt transformation
     */
    protected function _beforeTransform()
    {
        try {
            if (!empty($this->_imported_stylesheet) || $this->_imported_stylesheet !== $this->getXslDocument()) {
                $this->importStylesheet($this->getXslDocument());
            }
        } catch (\LogicException $e) {
        }
    }

    /**
     * Operations after xslt transformation
     */
    protected function _afterTransform()
    {

    }
}
