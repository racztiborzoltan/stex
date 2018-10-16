<?php
namespace Stex\CssSelector;

use Symfony\Component\CssSelector\Parser\Shortcut\ClassParser;
use Symfony\Component\CssSelector\Parser\Shortcut\ElementParser;
use Symfony\Component\CssSelector\Parser\Shortcut\EmptyStringParser;
use Symfony\Component\CssSelector\Parser\Shortcut\HashParser;
use Symfony\Component\CssSelector\XPath\Extension\HtmlExtension;
use Symfony\Component\CssSelector\XPath\Translator;
use Stex\CssSelector\XPath\Extension\CombinationExtension;

/**
 * Extended \Symfony\Component\CssSelector\CssSelectorConverter class
 *
 * Differences:
 * - new method: ->setTranslator()
 * - new method: ->getTranslator()
 * - overloaded extension: CombinationExtension
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class CssSelectorConverter extends \Symfony\Component\CssSelector\CssSelectorConverter
{
    private $translator;

    /**
     * @param bool $html Whether HTML support should be enabled. Disable it for XML documents
     */
    public function __construct(bool $html = true)
    {
        $this->setTranslator(new Translator());
        $translator = $this->getTranslator();

        if ($html) {
            $translator->registerExtension(new HtmlExtension($translator));
        }

        $translator
            ->registerParserShortcut(new EmptyStringParser())
            ->registerParserShortcut(new ElementParser())
            ->registerParserShortcut(new ClassParser())
            ->registerParserShortcut(new HashParser())
        ;

        $translator
            ->registerExtension(new CombinationExtension())
        ;
    }

    /**
     * Set CSS expresion translator object
     *
     * @param Translator $translator
     * @return self
     */
    public function setTranslator(Translator $translator): self
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Returns CSS expresion translator object
     *
     * @param Translator $translator
     * @return self
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * Translates a CSS expression to its XPath equivalent.
     *
     * Optionally, a prefix can be added to the resulting XPath
     * expression with the $prefix parameter.
     *
     * @param string $cssExpr The CSS expression
     * @param string $prefix  An optional prefix for the XPath expression
     *
     * @return string
     */
    public function toXPath($cssExpr, $prefix = 'descendant-or-self::')
    {
        return $this->getTranslator()->cssToXPath($cssExpr, $prefix);
    }
}
