<?php
 //Dynamic Image load to browser
 //Billy Richardson
 //July 13, 2010
 
 error_reporting(0);
   
 $directories = array(
  "global",
  "icons",
  "bg"  
 );
 
 foreach($directories as $dir) {
   $d = dir("../im/".$dir."") or die('//Preload Failed');
   while (false !== ($entry = $d->read())) {
     if($entry != '.' && $entry != '..' && $entry != 'Thumbs.db' && !is_dir($dir.$entry)) {
       echo "imagepreload".$count." = new Image(1,1);\n";
       echo "imagepreload".$count.".src = '/im/".$dir."/".urlencode($entry)."';\n";
       $count++;
	 }
   }
   $d->close();
 }


?>