<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    >

	<xsl:param name="title" select="'Simple Template XSLT Example'" />
	
	<xsl:output method="html" encoding="utf-8" indent="yes" />
	
	<xsl:template match="/">
		<tests>
			<test expected-value="Ben Harper">
				<!-- CSS expressions without ' or " enclosing characters -->
				<xsl:value-of select="@css[collection artist]"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<!-- CSS expressions with ' enclosing characters -->
				<xsl:value-of select="@css['collection artist']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<!-- CSS expressions with " enclosing characters -->
				<xsl:value-of select='@css["collection artist"]'></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="@css['collection artist']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="@css[' collection artist  ']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="@css[' collection artist']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="@css['collection > cd > artist']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="/collection/@css['artist']"></xsl:value-of>
			</test>
			
			<test expected-value="Ben Harper">
				<xsl:value-of select="@css['collection']/cd/artist"></xsl:value-of>
			</test>
		</tests>
	</xsl:template>

</xsl:stylesheet>