<?php

header("Content-type: text/xml");

require_once('./include/functions.php');

  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
  foreach($menu as $temp) {
   if(!preg_match("#https?:#",$temp['link'])) {
     $page = '../pages/'.$temp['link'].'.html';
     if(file_exists($page)) $date = date3339(filemtime($page)); else $date = date3339(time());
     echo '  <url>
    <loc>http://conversecounty.org'.$temp['link'].'</loc>
    <lastmod>'.$date.'</lastmod>
  </url>
';
   }
  }
  die("</urlset>");
