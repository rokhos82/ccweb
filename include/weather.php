<?php
//Weather.com RSS Reader
//by billy.richardson@gmail.com
//for conversecounty.org
//July 7, 2010

require("../include/functions.php");


/*----------------------
      USER SETTINGS 
-----------------------*/
$temp['location'] =   'USWY0047'              ; //USWY0047 = Douglas, Wyoming
$temp['cache'] =      '../include/caches/weather.xml'  ; //Cache Location
/*----------------------
      END SETTINGS
-----------------------*/

if(PROGRAM !== "converse") die('<b>ERROR:</b> This file cannot be accessed directly!');

//Does the cache exist and is up to date?
if(!file_exists($temp['cache']) || time()-filemtime($temp['cache']) > 3600) {
  //Download the feed to the cache
  file_put_contents($temp['cache'],return_page('http://rss.weather.com/weather/rss/local/'.$temp['location'].'?cm_ven=LWO&cm_cat=rss&par=LWO_rss'));
}

//Load the cache
$weather = file_get_contents($temp['cache']);
//Regex the info we need
preg_match('#([0-9]+).gif(.*?)/>(.*?), and ([0-9]+)#',$weather,$temp);

//Declare global weather array
$global['weather'] = array();
$global['weather']['image'] = $temp[1]; //Image
$global['weather']['detail'] = $temp[3]; // Detailed current
$global['weather']['tempF'] = round($temp[4]); //Tempature in Fahrenheit
$global['weather']['tempC'] = round(($temp[4]-32)/1.8); //Tempature in Celcius

//Free the unused variables from memory
unset($temp,$weather);

?>