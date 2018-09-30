# stex - Simple Template Extended Xslt

[![Build Status](https://travis-ci.org/racztiborzoltan/stex.svg?branch=master)](https://travis-ci.org/racztiborzoltan/stex)

- extended from PHP built in \XSLTProcessor class.
    - support all \XSLTProcessor features
- [PSR-11](https://www.php-fig.org/psr/psr-11/) container support
- some new small syntax

## PSR-11 Container support

```xml
<!-- new xslt function syntax -->
this:container(...)

this:container('CONTAINER_SCALAR_ITEM_NAME')

this:container('CONTAINER_FUNCTION_ITEM_NAME', 'PARAMETER_1', 'PARAMETER_2', '...')

this:container('CONTAINER_OBJECT_ITEM_NAME', 'METHOD_NAME', 'FIRST_PARAMETER', 'SECOND_PARAMETER', '...')
```

Usage example:
```xml
<xsl:value-of select="this:container('scalar_item_name')/>
<xsl:value-of select="this:container('function_item_name', 'foobar', 123)/>
<xsl:value-of select="this:container('object_item_name', 'method_name', 'arg_1', 'arg2')/>
```

This example code works only with \Stex\StexXsltProcessor class!

```
$xml = <<<EOB
<allusers>
 <user>
  <uid>bob</uid>
 </user>
 <user>
  <uid>joe</uid>
 </user>
</allusers>
EOB;
$xsl = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
     xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
     xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="utf-8" indent="yes"/>
 <xsl:template match="allusers">
  <html><body>
    <h2>Users</h2>
    <table border="1" cellspacing="0" cellpadding="5">
      <tr>
        <th>
            Name <br />(php:function)
        </th>
        <th>
            foobar text <br />(this:container)
        </th>
        <th>
            formatted datetime <br />(this:container)
        </th>
      </tr>
    <xsl:for-each select="user">
      <tr>
        <td>
            <xsl:value-of
                select="php:function('ucfirst',string(uid))"/>
        </td>
        <td>
            <xsl:value-of
                select="this:container('foo')"/>
        </td>
        <td>
            <xsl:value-of
                select="this:container('datetime', 'format', 'Y-m-d')"/>
        </td>
      </tr>
    </xsl:for-each>
    </table>
  </body></html>
 </xsl:template>
</xsl:stylesheet>
EOB;
$xmldoc = new \DOMDocument();
$xmldoc->loadXML($xml);
$xsldoc = new \DOMDocument();
$xsldoc->loadXML($xsl);

// define container and container items
$container = new Container();
$container->add('foo', 'bar');
$container->add('datetime', function(){
    return new \DateTime();
});

$proc = new StexXsltProcessor();
$proc->setContainer($container);
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsldoc);
echo $proc->transformToXML($xmldoc);
```

## CSS Select Example

```
$xml = <<<EOB
<!DOCTYPE html>
<html lang="en">
    <head><title>stex css select demo</title></head>
    <body>
        <section class="first">
            <header>
                <h1 class="title">First section title</h1>
            </header>
            <article>Lorem ipsum...</article>
        </section>
        <section class="second">
            <header>
                <h1 class="title">Second section title</h1>
            </header>
            <article>Dolor it...</article>
        </section>
    </body>
</html>
EOB;
$xsl = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" indent="yes"/>
 <xsl:template match="/">

  <xsl:value-of select="@css['section.first .title']"></xsl:value-of>
  <xsl:text>&#xa;</xsl:text>

  <xsl:value-of select="@css['section.second .title']"></xsl:value-of>
  <xsl:text>&#xa;</xsl:text>

  <xsl:for-each select="@css['section article']">
      <xsl:value-of select="."></xsl:value-of>
      <xsl:text>&#xa;</xsl:text>
  </xsl:for-each>

 </xsl:template>
</xsl:stylesheet>
EOB;
$xmldoc = new \DOMDocument();
$xmldoc->loadXML($xml);
$xsldoc = new \DOMDocument();
$xsldoc->loadXML($xsl);

$proc = new StexXsltProcessor();
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsldoc);
echo $proc->transformToXML($xmldoc);
```

## other examples

in `examples` directory!

--------------------------------------------------------------------------------

[http://racztiborzoltan.github.io](http://racztiborzoltan.github.io)
