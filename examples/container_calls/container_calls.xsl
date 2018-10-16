<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
     xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
     xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="utf-8" indent="yes"/>
 <xsl:template match="collection">
  <html><body>
    <h2>Artists</h2>
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
    <xsl:for-each select="//artist">
      <tr>
        <td>
            <xsl:value-of select="php:function('ucfirst',string(.))"/>
        </td>
        <td>
            <xsl:value-of select="this:container('foo')"/>
        </td>
        <td>
            <xsl:value-of select="this:container('datetime', 'format', 'Y-m-d H:i:s')"/>
        </td>
      </tr>
    </xsl:for-each>
    </table>
  </body></html>
 </xsl:template>
</xsl:stylesheet>
