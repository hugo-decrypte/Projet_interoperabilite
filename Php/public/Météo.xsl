<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" indent="yes" />

    <xsl:template match="/previsions">
        <div class="meteo-journee">
            <h2>Météo de la journée</h2>

            <!-- Sélection de quelques moments : matin (06h), midi (12h), soir (18h) -->
            <xsl:apply-templates select="echeance[@hour='06']" mode="moment">
                <xsl:with-param name="label" select="'Matin'" />
            </xsl:apply-templates>

            <xsl:apply-templates select="echeance[@hour='12']" mode="moment">
                <xsl:with-param name="label" select="'Midi'" />
            </xsl:apply-templates>

            <xsl:apply-templates select="echeance[@hour='18']" mode="moment">
                <xsl:with-param name="label" select="'Soirée'" />
            </xsl:apply-templates>

        </div>
    </xsl:template>


    <!-- Affichage d’un moment de la journée -->
    <xsl:template match="echeance" mode="moment">
        <xsl:param name="label" />

        <div class="moment">
            <h3><xsl:value-of select="$label"/></h3>

            <ul>
                <!-- Température -->
                <li>
                    <xsl:choose>
                        <xsl:when test="temperature/level/@val &lt; 0">
                            ❄️ Très froid
                        </xsl:when>
                        <xsl:when test="temperature/level/@val &lt; 10">
                            🧥 Froid
                        </xsl:when>
                        <xsl:otherwise>
                            🙂 Doux
                        </xsl:otherwise>
                    </xsl:choose>
                </li>

                <!-- Pluie -->
                <li>
                    <xsl:choose>
                        <xsl:when test="pluie &gt; 0.2">
                            🌧️ Pluie probable
                        </xsl:when>
                        <xsl:otherwise>
                            🌤️ Pas de pluie significative
                        </xsl:otherwise>
                    </xsl:choose>
                </li>

                <!-- Risque neige -->
                <li>
                    <xsl:choose>
                        <xsl:when test="risque_neige &gt; 0">
                            🌨️ Risque de neige
                        </xsl:when>
                        <xsl:otherwise>
                            ❌ Pas de neige
                        </xsl:otherwise>
                    </xsl:choose>
                </li>

                <!-- Vent -->
                <li>
                    <xsl:choose>
                        <xsl:when test="vent_rafales/level/@val &gt; 50">
                            💨 Vent fort
                        </xsl:when>
                        <xsl:when test="vent_moyen/level/@val &gt; 20">
                            🍃 Vent modéré
                        </xsl:when>
                        <xsl:otherwise>
                            🙂 Peu de vent
                        </xsl:otherwise>
                    </xsl:choose>
                </li>

            </ul>
        </div>
    </xsl:template>

</xsl:stylesheet>
