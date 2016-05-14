<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform exclude-result-prefixes="tei" version="1.0" xmlns="http://www.tei-c.org/ns/1.0" xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output
    method="html"
    encoding="UTF-8"
    doctype-public="-//W3C//DTD XHTML//EN" doctype-system="http://www.w3.org/TR/2001/REC-xhtml11-20010531" indent="yes" />
  <xsl:template match="node()|@*">
    <xsl:copy>
      <xsl:apply-templates select="node()|@*"/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="hi[@rend='i']">
    <i><xsl:apply-templates></xsl:apply-templates></i>
  </xsl:template>
  <xsl:template match="hi[@rend='sc']">
    <span class="sc"><xsl:apply-templates></xsl:apply-templates></span>
  </xsl:template>
  <xsl:template match="hi[@rend='sup']">
    <sup><xsl:apply-templates></xsl:apply-templates></sup>
  </xsl:template>
    <xsl:template match="note">
  </xsl:template>
</xsl:transform>