<?php
header('Content-Type: application/xml; charset=utf-8');
$base = 'https://axelclub.be';
$pages = [
    ['/', '1.0', 'weekly'],
    ['/a-propos', '0.8', 'monthly'],
    ['/groupes', '0.9', 'weekly'],
    ['/contact', '0.7', 'monthly'],
];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as [$loc, $priority, $freq]):
echo "  <url>\n";
echo "    <loc>$base$loc</loc>\n";
echo "    <changefreq>$freq</changefreq>\n";
echo "    <priority>$priority</priority>\n";
echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
echo "  </url>\n";
endforeach;
echo '</urlset>';
exit;
