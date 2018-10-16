<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    >

	<xsl:param name="title" select="'Simple Template XSLT Example'" />
	
	<xsl:output method="html" encoding="utf-8" indent="yes" />
	
	<xsl:template match="/">
		<!-- CSS expressions without ' or " enclosing characters -->
		<xsl:value-of select="@css[collection artist]"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		
		<!-- CSS expressions with ' enclosing characters -->
		<xsl:value-of select="@css['collection artist']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
	
		<!-- CSS expressions with " enclosing characters -->
		<xsl:value-of select='@css["collection artist"]'></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		
		<!-- 
		I recommend the enclosing varations!
		I suggest the enclosing character versions because they are valid for Xpath in syntax highlighters.
		
		I prefer the ' (apostrophe) character for css expressions.
		-->

		<xsl:value-of select="@css['collection artist']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css[' collection artist  ']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css[' collection artist']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css['collection > cd > artist']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="/collection/@css['artist']"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css['collection']/cd/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css['collection']/@css['cd[format=mini]']/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<xsl:value-of select="@css['cd.first']/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<!-- complex css expression and valid xpath -->
		<xsl:value-of select="@css['collection']/@css[cd[format='maxi mini']]/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<!-- complex css expression with escape and valid xpath -->
		<xsl:value-of select="@css['collection']/@css[cd[format=&apos;maxi mini&apos;]]/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		<!-- complex css expression and invalid xpath -->
		<xsl:value-of select="@css['collection']/@css['cd[format=&apos;maxi mini&apos;]']/artist"></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
		
		<!-- apply the other templates -->
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
	
    <xsl:template match="@css['cd.first artist']">
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
        <xsl:value-of select="'css selector in match attribute: &#xa;'"></xsl:value-of>
        <xsl:value-of select="."></xsl:value-of>
		<xsl:text>&#xa;--------------------------------&#xa;</xsl:text>
    </xsl:template>


    <!-- default template for all tag and attribute -->
	<xsl:template match="node()|@*">
		<xsl:apply-templates></xsl:apply-templates>
	</xsl:template>
    
</xsl:stylesheet>