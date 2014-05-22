<?php
//Popular Page Tracker
//By billy.richardson@gmail.com
//For conversecounty.org
//July 21, 2010

//current
$temp['current'] = $_GET['page'].'/'.$_GET['sub'].'/'.$_GET['subloc'];

//read popular include
$popular = array();
@include("./db/populardb.php"); //$global['pagelocation']  $global['pagetitle']

//write header
file_put_contents("./db/populardb.php","<?php\n//Page Generated at ".date('F j, Y, g:i a')."\n\n");

//loop for each item
foreach($popular as $temp['loop']) {
 if($temp['loop'][1] == $temp['current']) { $temp['loop'][0]++; $trip = true; }
   file_put_contents("./db/populardb.php","\$popular[] = array(".$temp['loop'][0].",'".addslashes($temp['loop'][1])."','".addslashes($temp['loop'][2])."');\n",FILE_APPEND);
}

//if current page hasn't been accessed
if(!$trip) {
   file_put_contents("./db/populardb.php","\$popular[] = array(1,'".addslashes($temp['current'])."','".addslashes($global['pagetitle'])."');\n",FILE_APPEND);
}

//write file end
file_put_contents("./db/populardb.php","\n?>",FILE_APPEND);

//destroy temp var
unset($temp);

?>