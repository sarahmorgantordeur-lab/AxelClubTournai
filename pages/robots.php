<?php
header('Content-Type: text/plain');
header('X-Robots-Tag: noindex');
echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /admin/\n";
echo "Disallow: /auth/\n";
echo "Disallow: /dashboard\n";
echo "Disallow: /my-attendance\n";
echo "\n";
echo "Sitemap: https://axelclub.be/sitemap.xml\n";
exit;
