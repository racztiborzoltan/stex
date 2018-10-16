<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:param name="title" select="'Simple Template XSLT Example'" />
	
	<xsl:output method="html" encoding="utf-8" indent="yes" />
	
	<xsl:template match="collection">
		<table style="border-collapsing: collapse;">
			<caption>
				<xsl:value-of select="$title" />
			</caption>
			<tr>
				<th>Title</th>
				<th>Artist</th>
				<th>Year</th>
			</tr>
			<xsl:apply-templates/>
		</table>
	</xsl:template>

	<xsl:template match="cd">
		<tr>
			<td style="padding: 0.5em;">
				<xsl:value-of select="title" />
			</td>
			<td style="padding: 0.5em;">
				<xsl:value-of select="artist" />
			</td>
			<td style="padding: 0.5em;">
				<xsl:value-of select="year" />
			</td>
		</tr>
	</xsl:template>

</xsl:stylesheet>