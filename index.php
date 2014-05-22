<?php
//Converse County, Wyoming
//index.php
//August, 2010
//Created by Billy Richardson - billy.richardson+converse@gmail.com

$expires = 60*5;
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header("Pragma: public");

$mtime = 0;

//start_timer();
if(!isset($_GET['page'])) $_GET['page'] = "";


$global = array();
$global['page'] = trim(ereg_replace('[^a-z0-9/_-]', "",str_replace(" ","_",strtolower(strlen($_GET['page'])>0?$_GET['page']:"index"))),' /');
$splice = explode("/",$global['page']);
//$global['pagetitle'] = $menu[$global['page']][0];
$global['pagelocation'] = "./pages/".$global['page'].".html";
$global['cachelocation'] = "./pages/cache/".$global['page'].".htm";

//Record Hit
/*if($global['page']=="index") {
  @file_put_contents("./uhits.txt",ip2long($_SERVER['REMOTE_ADDR']).",".time().",\"".htmlentities($_SERVER['HTTP_USER_AGENT'])."\",\"".$_SERVER['HTTP_REFERER']."\"\n",FILE_APPEND);
}*/

$secexpire = 43200; //12 hours cache

//TEMP
if(time()<1299913200)
  $secexpire = 5400;
//END TEMP

$cachetime = @filemtime($global['cachelocation']);
if(!(intval($_GET['error']) > 0) && $global['page'] != "search" && file_exists($global['cachelocation']) && $cachetime+$secexpire > time() && filemtime($global['pagelocation']) < $cachetime && $GetFile = fopen($global['cachelocation'],"r")) { 
    while (!feof($GetFile)){
      echo fgets($GetFile,128);
			//flush();
    }
    flush();
    fclose($GetFile);
    die('  <!-- Cache - Expires: '.(($cachetime+$secexpire)-time()).' sec -->');
    //die('  <!-- Cache: '.end_timer().' - Expires: '.(($cachetime+$secexpire)-time()).' sec -->');
}

require_once('./include/functions.php');
start_timer();

/*
    if(file_exists($global['cachelocation'])) { 
      echo file_get_contents($global['cachelocation']);
      die(end_timer());
      return true;
    }
*/

require('./include/menu.php');

$page = array();
if(!isset($_GET['error'])) $_GET['error'] = 0;
switch(intval($_GET['error'])) {
default:
 $splices = implode("/",$splice);
 $page['location'] = './pages/'.$splices.".html";
 $page['govbypass'] =  './pages/gov-admin/'.$splices.".html";
 
 /*if(file_exists($page['govbypass']))
   $govbypass = true;
 else
   $govbypass = false;*/
	 
  if(file_exists($page['location']))
	  $location = true;
	else
	  $location = false;
 

 if($location /*|| $govbypass*/)
 {
   /*if($govbypass && !$location) 
	   $page['location'] = $page['govbypass'];*/

   $page['contents'] = custom_tags(file_get_contents($page['location']));
   if(!isset($page['title'])) $page['title'] = titlestring(end($splice));
   $page['bread'] = breadcrumb();
   //record_hit();
   ob_start(); $makecache = true; 
 } else {
   notfound();
 }

break;
case 404:
  notfound(); 
break;
case 403:
 /*header('HTTP/1.1 403 Forbidden');
 $page['title'] = "Forbidden";
 $page['contents'] = "<h5>403 - ".$page['title']."</h5>You are forbidden to view this page.";*/
 //fake 404
 notfound(); 
break;
case 401:
 header('HTTP/1.1 401 Unauthorized');
 $page['title'] = "Unauthorized";
 $page['contents'] = "<h5>401 - ".$page['title']."</h5>You are unauthorized to view this page.";
break;
case 400:
 header('HTTP/1.1 400 Bad Request');
 $page['title'] = "Bad Request";
 $page['contents'] = "<h5>400 - ".$page['title']."</h5>Your browser send a bad request to our server.";
break;
case 500:
 header('HTTP/1.1 500 Internal Server Error');
 $page['title'] = "Internal Server Error";
 $page['contents'] = "<h5>500 - ".$page['title']."</h5>The server is experiencing technical difficulties. Sorry for the inconvenience.";
break;
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
      <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
      <meta http-equiv="content-language" content="en" />
      <meta name="copyright" content="<?php echo date('Y'); ?> Converse County" />
      <meta name="author" content="Billy Richardson, billy.richardson+converse@gmail.com" />
<?php if($global['page']=="index") echo "      <meta name=\"description\" content=\"Information about the offices and operation of government in Converse County, Wyoming.\" />\n"; ?> 
      <style type="text/css">
        <!--
          #maindiv { background-image: url('/im/bg/<?php echo random_header(); ?>');  }
        -->
      </style>

      <link rel="stylesheet" type="text/css" href="/css/main.css?v-4" />
      <link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />
      <script type="text/javascript" src="/js/iepngfix_tilebg.js"></script>
      <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
      <script type="text/javascript" src="/js/ddsmoothmenu.js"></script>
      <script type="text/javascript">ddsmoothmenu.init({mainmenuid: "hmenu", orientation: 'h', classname: 'ddsmoothmenu', contentsource: "markup"})</script>

      <title><?php echo ($global['page']=="index"?"":$page['title']." - "); ?>Converse County, <?php echo ($global['page']=="index"?"Wyoming":"WY"); ?></title>
    </head>
    <body>
        <table summary="" id="main">
        <tr><td id="maintd">
         <div id="maindiv">
          <table summary="" id="maincenter" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td id="top">
              <div class="noprint searchbox">
                <form action='/search' method='get'>
                  <input type='text' class="query" name='q' size='30' maxlength='100' id='pagetopsearch' value='' onblur='if(this.value=="") {this.value="Search Converse County";this.style.color="#555555"}' onfocus='if(this.value=="Search Converse County") {this.value = "";this.style.color="#000000"}' />
                  <script type="text/javascript">var searchbox = document.getElementById('pagetopsearch'); if(searchbox.value=="") {searchbox.value="Search Converse County"; searchbox.style.color="#555555"}</script>
                  <input type='submit' value='search' />
                </form>
              </div>
              <table summary="Print Header" border="0" class="print printheader"><tr><td><img src="/im/global/logo-print.jpg" class="print-image" alt="" /></td><td><h1>Converse County, Wyoming</h1></td><td><span class="tagline">ConverseCounty.org</span></td></tr></table></td>
            </tr>
            <tr class="noprint">
              <td id="top-menu" class="container4"><div id="hmenu" class="ddsmoothmenu">
<?php echo build_menu($menu); ?>
              </div></td>
            </tr>
            <tr class="noprint">
              <td id="top-last"><?php if(isset($page['bread'])) print $page['bread']; ?></td>
            </tr>
            <tr>
              <td id="content">
<!--STARTCONTENT-->
<?php
echo $page['contents'];
if($showreader) echo '<a class="noprint getadobe" href="http://get.adobe.com/reader/"><img src="/im/global/adobe-reader.png" alt="Get Adobe Reader" title="This page contains PDF files which require Adobe Reader to view" /></a>';
?> 
<!--ENDCONTENT-->
              </td>
            </tr>
             <tr class="noprint">
              <td id="bot-last">
                &copy; Converse County, Wyoming <?php echo date("Y"); ?><br/>
                107 No. 5th St., Suite 114 -  
                Douglas, WY 82633-2448 -
                (307) 358-2244<br />
                <a href="mailto:Lucile.Taylor@conversecountywy.gov">Lucile.Taylor@conversecountywy.gov</a>
              </td>
            </tr>
          </table>
         </div> 
        </td>
       </tr>
      </table>
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-3073576-3', 'conversecounty.org');
        ga('send', 'pageview');
      </script>
    </body>
  </html>
<?php

  if($makecache) {
    cache(ob_get_contents());
    ob_flush();
    @ob_end_clean();
  }
  
  die('  <!-- Generated: '.end_timer().' -->');
?>