<?php

namespace Stex\CssSelector\XPath\Extension;

use Symfony\Component\CssSelector\XPath\XPathExpr;

/**
 * XPath expression translator combination extension.
 */
class CombinationExtension extends \Symfony\Component\CssSelector\XPath\Extension\CombinationExtension
{
    /**
     * @return XPathExpr
     */
    public function translateDescendant(XPathExpr $xpath, XPathExpr $combinedXpath): XPathExpr
    {
        return $xpath->join('//', $combinedXpath);
    }
}
