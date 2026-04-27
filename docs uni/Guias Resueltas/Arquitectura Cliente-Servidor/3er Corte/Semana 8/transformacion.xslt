<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <div style="font-family: Arial, sans-serif;">
      <h2>Resultado de la Operación</h2>
      
      <div style="background-color: #eee; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <strong>ID Transacción:</strong> <xsl:value-of select="/mensaje/mensajes_control/id_transaccion"/><br/>
        <strong>Fecha/Hora:</strong> <xsl:value-of select="/mensaje/mensajes_control/timestamp"/><br/>
        <strong>Emisor:</strong> <xsl:value-of select="/mensaje/mensajes_control/emisor"/>
      </div>

      <xsl:choose>
        <!-- Cuando es Response -->
        <xsl:when test="/mensaje/response">
          <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
            <h3>¡Respuesta del Servidor!</h3>
            <p><strong>Operación:</strong> <xsl:value-of select="/mensaje/response/operacion"/></p>
            <p><strong>Estado:</strong> <xsl:value-of select="/mensaje/response/estado"/></p>
            
            <xsl:if test="/mensaje/response/datos/item">
              <table style="width: 100%; border-collapse: collapse; margin-top: 10px; background: white;">
                <tr style="background-color: #c3e6cb;">
                  <th style="padding: 8px; border: 1px solid #b1dfbb; text-align: left;">Clave</th>
                  <th style="padding: 8px; border: 1px solid #b1dfbb; text-align: left;">Valor</th>
                </tr>
                <xsl:for-each select="/mensaje/response/datos/item">
                  <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><xsl:value-of select="clave"/></td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong><xsl:value-of select="valor"/></strong></td>
                  </tr>
                </xsl:for-each>
              </table>
            </xsl:if>
          </div>
        </xsl:when>
        
        <!-- Cuando es Error -->
        <xsl:when test="/mensaje/error">
          <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
            <h3>Ocurrió un Error</h3>
            <p><strong>Estado:</strong> <xsl:value-of select="/mensaje/error/estado"/></p>
            <p><strong>Código:</strong> <xsl:value-of select="/mensaje/error/codigo"/></p>
            <p><strong>Mensaje:</strong> <xsl:value-of select="/mensaje/error/mensaje_error"/></p>
          </div>
        </xsl:when>
      </xsl:choose>
    </div>
  </xsl:template>

</xsl:stylesheet>
