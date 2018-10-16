<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl">
<xsl:output method="text" encoding="utf-8"/>
	<xsl:template match="collection">
		<xsl:for-each select="cd">
			<xsl:value-of select="php:function('strtoupper',string(artist))"/>
			<!-- output new line: -->
			<xsl:value-of select="'&#xa;'"/>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
