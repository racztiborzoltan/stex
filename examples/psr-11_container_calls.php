<?php
use Stex\StexXsltProcessor;
use League\Container\Container;

//
// Original code: http://php.net/manual/en/xsltprocessor.registerphpfunctions.php
//

require_once '../vendor/autoload.php';

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
