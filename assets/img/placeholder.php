<?php
/**
 * Placeholder SVG para imágenes que no cargan
 */
header('Content-Type: image/svg+xml');
?>
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
  <rect width="200" height="200" fill="#f0f0f0"/>
  <g transform="translate(100, 100)">
    <circle cx="0" cy="-30" r="25" fill="#0071e3"/>
    <path d="M -50,30 Q 0,50 50,30" fill="#0071e3"/>
    <text x="0" y="60" font-size="14" fill="#999" text-anchor="middle">Imagen no disponible</text>
  </g>
</svg>
