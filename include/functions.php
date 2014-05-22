<?php
//Functions.php
//Functions for conversecounty.org
//Functions are used in parsing files for display on website.

//Billy.Richardson@gmail.com
//2011-2014


error_reporting(0);

$showreader = false;

$filetypes = "xls|pdf|docx?|xlsx?|pptx?";
$imagetypes = "jpe?g|gif|png|tiff";

function start_timer() {
  global $mtime,$starttime;
  $mtime = microtime();
  $mtime = explode(' ', $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $starttime = $mtime;
}

function end_timer() {
  global $mtime,$starttime;
  $mtime = microtime();
  $mtime = explode(" ", $mtime);
  $mtime = $mtime[1] + $mtime[0];
  $endtime = $mtime;
  return round($endtime - $starttime,3);
}

if (!function_exists('file_put_contents')) {
   @define("FILE_APPEND","a+");
   function file_put_contents($file, $contents = '', $method = 'w+') {
      $file_handle = fopen($file, $method);
      fwrite($file_handle, $contents);
      fclose($file_handle);
      return true;
   }
}

// Checks if the file is an image. If not, it won't be added to the image list
function is_image($filename){
    $filename    =    strtolower($filename) ;
    $ext        =    split("[/\\.]", $filename) ;
    $n            =    count($ext)-1;
    $ext        =    $ext[$n];

    if($ext == "jpg" || $ext == "JPG" || $ext == "jpeg" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
        return true;
    } else {
        return false;
    }
}


if (!function_exists('file_get_contents')) {
  function file_get_contents($filename) {

    $fhandle = fopen($filename, "r");
    $fcontents = fread($fhandle, filesize($filename));
    fclose($fhandle);

    return $fcontents;
   }
}

function date3339($timestamp=0) {

    if (!$timestamp) {
        $timestamp = time();
    }
    $date = date('Y-m-d\TH:i:s', $timestamp);

    $matches = array();
    if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $timestamp), $matches)) {
        $date .= $matches[1].$matches[2].':'.$matches[3];
    } else {
        $date .= 'Z';
    }
    return $date;
}

function cache($input=false) { 
 global $splice,$global,$cachename;
  /*if($cachename) {
    $splice = $cachename;
  }*/
  $createcache = true;
  @mkdir('./pages/cache');
  $kat = "./pages/cache/";
  if(isset($splice)&&count($splice)>0) {
    for($k=0;$k<count($splice);$k++) {
      $kat .= $splice[$k]."/";
      if($k+1==count($splice)) {
        $file = trim($kat,"/").".htm";
        /*if(!file_exists($file)) {
          if(!$createcache) return false;
          else {*/
            file_put_contents($file,$input);
       /*   }
        } else {
          echo file_get_contents($file).end_timer();
          return true;
        }*/
      } else {
        if(!is_dir(trim($kat,"/"))) {
          mkdir($kat);
        }
      }
    }
  }
}

function random_header() {
  $list = array(
    0 => "douglas",
    1 => "field-houses",
    2 => "natural-bridge",
    3 => "north-platte",
    4 => "north-platte-steam",
  );
  $image = floor(date('j')/3) % count($list);
  return $list[$image].".jpg";
}

function link_email($email,$ismail) {
  if($ismail == "mailto:") return $email;
  return '<a href="mailto:'.$email.'" class="emaillink">'.$email.'</a>';
}

function linkfile($type,$link,$name) {
  global $splices,$filetypes,$showreader;
  $link = preg_replace("/.(".$filetypes.")/","",$link);
  $link = str_replace(" ","%20",$link);
  if(substr($link,0,1)!="/") $link = '/'.$splices.'/'.$link;
  if($type=="pdf") $showreader = true;
  return '<a href="'.$link.'.'.$type.'" class="filelink '.$type.'link">'.$name.'</a>';
}

function image($link,$ext,$size='small') { 
  global $splices,$imagetypes;
  $link = preg_replace("/.(".$imagetypes.")/i","",$link);
  if(substr($link,0,1)!="/") $link = '/'.$splices.'/'.$link;
  if(file_exists('./images'.$link.'.'.$ext)) {
    if($size=='large') {$width=350;$height=300;} else {$width=250;$height=200;}
    if(!file_exists('./images'.$link.'_thumb.'.$ext)) if(!createthumbnail('./images'.$link,$ext,$width,$height)) return "IMAGE NOT FOUND";
    return '<a href="'.$link.'_full.'.$ext.'" class="imagethumb"><img class="shadow" src="'.$link.'_thumb.'.$ext.'" alt="" /></a>';
  } else return "IMAGE NOT FOUND";
}

function link_emails_text($text) {
  return preg_replace("/(mailto:)?([!#$%&'\*+-\=?^_`{|}~.\/A-Za-z0-9]+)\@([A-Za-z0-9.-]+)/e","link_email('\\2@\\3','\\1');",$text);
}

function headerimage($text,$type) {
 global $page;
 if($type=="page") $page['title'] = stripslashes($text);//http://billywr.com/dhs/apps/titlegen/run.php?act=print&amp;
 
 if(isset($type)) $type = str_replace("_","",strtolower($type)); else $type = "";

  switch($type) {
    default:
      $type = "page";
    break;
    case "sub":
      $type = "sub";
    break;
    case "dep":
      $type = "dep";
    break;
    case "dark":
      $type = "dark";
    break;
  }

  $text = stripslashes(str_replace("_"," ",$text));
  if(empty($text)) $text = "ERROR";

  $cache = '/im/cache/titles/'.md5($type.$text).'.gif';

  if(file_exists('.'.$cache)) {
    $location = $cache;
  } else {
    $location = '/apps/image.php?type='.$type.'&amp;text='.urlencode($text);
  }

  return '<span class="'.($type=='sub'||$type=='dep'?"headersubimage":($type=="dark"?"headerdarkimage":"headerimage")).'">'.htmlentities($text)."</span>";
 //return '<img src="'.$location.'" class="'.($type=='sub'||$type=='dep'?"headersubimage":"headerimage").'" alt="'.htmlentities($text).'" title="'.htmlentities($text).'" />';
 //return '<img src="/im/title/'.($type=='sub'?"sub_":"").''.str_replace(" ","%20",$text).'.gif" class="'.($type=='sub'?"headersubimage":"headerimage").'" alt="'.htmlentities($text).'" title="'.htmlentities($text).'" />';
 //return '<h4 class="header">'.$text.'</h4>';
}

$menuparentcache = false;
function detectmenusubs() {
 global $menu,$menuparentcache;
 if($menuparentcache) return $menuparentcache;
 else {
   $menuparentcache = array();
   foreach($menu as $blarg) {
     if(isset($blarg['parent'])) {
       $menuparentcache[$blarg['parent']]++;
     }
   }
   return $menuparentcache;
 }
}

function menulist($parent,$top=true) {
  global $menu;
  $text = "<ul class='linklist'>";
  $count = 0;
  $subs = detectmenusubs();
  for ( $i = 1; $i <= multimax($menu); $i++ ) {
    if(isset($menu[$i]['parent']) && $menu[$i]['parent'] == $parent) {
      $count++;
      $text .= '<li><a href="'.$menu[$i]['link'].'"'.($top?" class='listtop'":"").'>'.$menu[$i]['text']."</a>";
      if($subs[$i]>0) $text .= menulist($i,false);
      $text .= "</li>\n";
    }
  }
  $text .= "</ul>";
  return ($count>0?$text:false);
}

function titlestring($text) {
  return ucwords(preg_replace("#([ ]+)#"," ",trim(ereg_replace("[^a-z0-9]", " ", strtolower($text)))));
}

function breadcrumb() {
  global $splice, $page;
  $data = '<ul class="breadcrumb">'; //<li><a href="/" class="breadmain">Converse County</a></li>';
  if(count($splice)>1)
    for($k=0;$k<count($splice);$k++) {
     $temp = "";
     for($t=0;$t<=$k;$t++) {
       $temp .= $splice[$t].'/';
     }
     $temp = trim($temp,'/');
     $link = './pages/'.$temp.'.html';
     if(file_exists($link) && $k+1!=count($splice)) {
       preg_match("#<pageheader>(.*?)<\/pageheader>#",file_get_contents($link),$loc);
       if(isset($loc[1]) && strlen($loc[1])>0) $loc = $loc[1]; else $loc = titlestring($splice[$k]);
       $loc = "<a href='/".$temp."'>".$loc."</a>";
     } elseif($k+1==count($splice)) {
       $loc = "<span>".$page['title']."</span>";
     } else {
       $loc = "<span>".titlestring($splice[$k])."</span>";
     }
    $data .= "<li".($k==0?' class="breadmain"':'').">".$loc."</li>";
    }
  return $data."</ul>";
}

$result = array();

//ReturnSearch() 
//by Billy Richardson
function returnsearch($terms, $curdir='/') {
global $result;
$dir = './pages';
if ($handle = opendir($dir.$curdir)) {
    while (false !== ($file = readdir($handle))) {
        if($file == "cache" || $file == "." || $file == ".." || $file == "search.html" || $file == "index.html") continue;
        $ofile = $file;
        $file = $curdir.$file;
        $ext = end(explode('.', $file));
        if ($ext == "html" && !is_dir($dir.$file)) {
           $found = array();
           $count = $loop = 0;
           $header = $returnline = "";
            $GetFile = @fopen($dir.$file,"r");
            if($GetFile) {
               while (!feof($GetFile)){
                 $line = fgets($GetFile,4096);
                 if(preg_match("#<pageheader>(.*?)<\/pageheader>#",$line,$temp)) { $header = $temp; }
                 $line = strip_tags(str_replace(">","> ",$line));
                 if($loop==0) $firstline = $line;
                 $loop++;
                 foreach($terms as $d => $temp) {
                  if(preg_match("/".$temp."/is",$line)) {
                    $found[$d] = true;
                    $returnline = $line;
                    $count++;
                  }
                }
               }
             }
             fclose($GetFile);
          $all = false;
          if($returnline == "") $returnline = $firstline;
          if($header[1]) $header = $header[1]; else $header = titlestring(str_replace(".html","",$ofile));
          foreach($terms as $d => $temp) { if(preg_match("/".$temp."/is",$header)) { $count+=100; $all = true; } elseif(preg_match("/".$temp."/is",$file)) { $count+=50; $all = true; } elseif(isset($found[$d])) $all = true; else { $all = false; break; } }
          for($k=0;$k<=strlen($file);$k++) {
            $count-= 1;
          }
          if($all) $result[] = array($count,$file,$header,trim($returnline));
        } elseif(is_dir($dir.$file)) {
          returnsearch($terms,$file."/");
        }
    }
    closedir($handle);
}
return $result;
}

function subval_sort($a,$subkey) {
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
	asort($b);
	foreach($b as $key=>$val) {
		$c[] = $a[$key];
	}
	return $c;
}

function searchresults($search=false) {
  global $_GET, $cacheinfo, $searchresultcount;
  if($search=="url") $_GET['q'] = strip_tags($_SERVER['REQUEST_URI']);
    else $_GET['q'] = strip_tags($_GET['q']);
  $term = preg_replace("([ ]+)", " ", trim(ereg_replace("[^A-Za-z0-9]", " ", $_GET['q'])));
  $terms = explode(" ",$term);
  $terms = array_unique($terms);
  if(strlen($term)<3) { return "<h3 style='color:red'>ERROR: Your search needs to be more than 3 characters.</h3>"; }
  elseif(strlen($term)>100) { return "<h3 style='color:red'>ERROR: Your search contains too many characters.</h3>"; }
  $searchfile = './pages/cache/search/'.strtolower(str_replace(" ","_",$term)).'.dat';
  @mkdir("./pages/cache/search");

  $searchexpires = @filemtime($searchfile);
  if(file_exists($searchfile) && $searchexpires+86400 > time()) {
    $return = "<!--Search from cache - Expires: ".(($searchexpires+86400)-time())."-->\n";
    $results = unserialize(file_get_contents($searchfile));
  } else {
    $return = "<!--Searching-->\n";
    //foreach($terms as $temp) if(strlen($temp)<3) { return "<h3 style='color:red'>ERROR: Your search contained a word less than 3 characters.</h3>"; }
    if($_GET['type']=="pdf") {
      echo "PDF";
      $results = subval_sort(returnsearchpdf($terms),0);
    } else
       $results = subval_sort(returnsearch($terms),0);
    rsort($results);
    //file_put_contents($searchfile,serialize($results));
  }
  $searchresultcount = count($results);
  if($searchresultcount == 0) { return $return."<h3 style='color:red'>No results found</h3>"; }
  foreach($results as $data) {
    $result = $disresult = str_replace(".html","",$data[1]);
    $descstart = 0;
    foreach($terms as $temp) {
      $data[2] = preg_replace("/(".$temp.")/i","<b>\${1}</b>",$data[2]);
      $data[3] = preg_replace("/(".$temp.")/i","<b>\${1}</b>",$data[3]);
      $te = strpos(strtolower($data[3]),strtolower($temp));
      if($te > 0) { $descstart = $te; $word = $temp; }
      $disresult = preg_replace("/(".$temp.")/i","<b>\${1}</b>",$disresult);
    }
    if(strlen($data[3]) > 100) $data[3] = showexcerpt($data[3],100,$descstart,strlen($word));
    $return .= '<a style="font-size:12pt;" href="'.$result.'">'.$data[2].'</a><!--<span style="font-size:9pt;color:#fff;">Rank: '.$data[0].'</span>--><br><span style="font-size:10pt;">'.$data[3].'</span><br><span style="color: #333;font-size:10pt;">http://conversecounty.org'.$disresult."</span><br><br>\n";
  }

  return $return;
}

function searchterm($type) {
  global $_GET;
  $_GET['q'] = strip_tags($_GET['q']);
  if($type=="url") return urlencode($_GET['q']);
  elseif($type=="html") return htmlentities($_GET['q']);
  else { return chunk_split($_GET['q'],25); }
}

function showexcerpt($content, $maxchars, $show=0, $length) {  

$content .= " ";

if (strlen($content) > $maxchars) {  
 
$content= substr($content, intval($show-25), $maxchars);  
$pos = strrpos($content, " ");  
 
if ($pos>0) {  
$content = substr($content, strpos($content, " ")  , $pos);  
}  
 
return $content . "...";  
 
} else {  
 
return $content;  
 
}  
 
}

function notfound() {
 global $page;
 header("HTTP/1.0 404 Not Found");
 $page['title'] = "Page Not Found";
 $page['contents'] = custom_tags("<h5>404 - Page Not Found</h5>Sorry, the page that you requested couldn't be found on our server.<br /><br />It's possible that this page has been moved or renamed.<br /><br />You can use your browser's <i>back</i> button, <a href='/'>start at our homepage</a> or search our site:<br><br><form action='/search' method='get'><input type='text' name='q' size='40' maxlength='100' /> <input type='submit' value='search' /></form>");

}

function time_remove($time, $data)
{
  if(time()>=$time)
    return "";
  else
    return stripslashes($data);
}

function place_icon($type,$expire)
{
  if(intval($expire) != 0 && time() > intval($expire)) return "";
  $type = strtolower($type);
  return " <span class='badge badge_".$type."'>&nbsp;</span>";
}

function current_topic($topicid)
{
  switch($topicid)
	{
    default:
		 $id = ucwords(strtolower(str_replace(array("_","-"),"",$topicid)));
		 $link = false;
		 $icon = false;
		 break;
		case "agenda":
		  $id = "Meeting Agenda";
			$icon = "calendar";
			$link = "/gov-admin/county-commissioners/agenda";
		break;
		case "minutes":
		  $id = "Meeting Minutes";
			$icon = "announcements";
			$link = "/gov-admin/county-commissioners/meeting-minutes";
		break;
		case "publichearing":
		  $id = "Public Hearing";
			$icon = "government_and_administration";
			$link = "/gov-admin/business/public-hearing";
		break;
		case "notice":
		  $id = "Notices";
			$icon = "announcements";
			$link = "/gov-admin/business/notices";
		break;
	}
	
	return "<span class='doctopic".($icon!=false?' menu_icon_'.$icon:'')."'>Topic: ".($link!=false?'<a href="'.$link.'">':'').$id.($link!=false?'</a>':'')."</span>";
}

function file_version($file)
{
  return base_convert(filemtime($file)%10000,10,36);
}

function news_item($time,$data,$topic=false)
{
  $data = stripslashes($data);
  $time = explode(" ",$time);
  $month = $time[0];
  $day = $time[1];
  return <<<LOL
<table class="newsitem">
 <tr>
  <td class="date">
    <div class="date-inside">
      <div class="date-month">{$month}</div>
      <div class="date-day">{$day}</div>
    </div>
  </td>
  <td class="newstext">
{$data}
   </td>
 </tr>
</table>
LOL;
}

function custom_tags($text) {
  global $filetypes,$imagetypes,$searchresultcount;
  $text = str_replace(array(chr(145),chr(146),chr(147),chr(148)), array("'","'","\"","\""), $text); //special char fix
  $text = str_ireplace("pmccul@state.wy.us","pam.mccullough@wyo.gov", $text);
	$text = str_ireplace("lucile.taylor@conversecounty.org","lucile.taylor@conversecountywy.gov", $text);
  $text = str_ireplace("Dixie.Huxtable@conversecounty.org","Dixie.Huxtable@conversecountywy.gov", $text);
	$text = str_ireplace("Assessor@conversecounty.org","Assessor@conversecountywy.gov",$text);
	$text = str_ireplace("ccr@conversecounty.org","RoadandBridge@conversecountywy.gov",$text);
	$text = str_ireplace("Dawn.rittel@conversecounty.org","dawn.rittel@conversecountywy.gov",$text);
  $text = preg_replace("/<topic=([\"|'])([-A-Za-z0-9_ ]+)([\"|'])\/?>/ise","current_topic('\\2')",$text);
  $text = preg_replace("/<!--\*(.*?)\*-->/ise","",$text);
  $text = preg_replace("/<time=(\"|')?([0-9]+)(\"|')?>(.*?)<\/time>/ise","time_remove('\\2','\\4')",$text);
  $text = preg_replace("/<newsitem=(\"|')(.*?)(\"|')>(.*?)<\/newsitem>/ise","news_item('\\2','\\4')",$text);
  $text = link_emails_text($text);
  $text = preg_replace("/<icon=(\"|')([A-Za-z]+)(\"|')(\/)?>(([0-9]+)<\/icon>)?/ise","place_icon('\\2','\\6')",$text);
  $text = preg_replace("/<application>([a-z]+)<\/application>/ise","loadapp('\\1')",$text);
  $text = preg_replace("/<(url|html)?term>/ise","searchterm('\\1')",$text);
  $text = preg_replace("/<(url)?searchresults(.*?)?>/ise","searchresults('\\1')",$text);
  $text = preg_replace("/<(page|sub|dep|dark)?header>(.*?)<\/(page|sub|dep|dark)?header>/ie","headerimage('\\2','\\1')",$text);
  $text = preg_replace("/<list>([0-9]+)<\/list>/ie","menulist(\\1)",$text);
  $text = preg_replace("/<(".$filetypes.") href=(\"|')(.*?)(\"|')>(.*?)<\/(".$filetypes.")>/ise","linkfile('\\1','\\3','\\5')",$text);
  $text = preg_replace("/<(".$imagetypes.")( size=\"(small|large)\")? (src|href)=(\"|')(.*?)(\"|')( \/|\/)?>/ise","image('\\6','\\1','\\3')",$text);
  $text = str_replace("<resultcount>",$searchresultcount,$text);
  $text = str_replace("<br>","<br/>",$text);

  return $text;
}

function cleanpath($fname) {
  return preg_replace("/[^a-zA-Z0-9 \s]/","",$fname);
}

function loadapp($app) {
  $apppath = './apps/'.cleanpath($app).'.php';
  if(file_exists($apppath)) {
    include($apppath);
    return $application['out'];
  } else {
    return "Application load error! Please try this page again in a few minutes.";
  }
}

function cleanstring($text,$type="none") {
 if($type=="url") $match = "[^a-z0-9/_-]";
   else $match = "[^a-z0-9_]";
 return ereg_replace($match, "",str_replace(" ","_",strtolower($text)));
}

function multimax( $array ) {
    $max = 0;
    foreach( $array as $key => $value ) {
      if($key>$max) $max = $key;
    }
    return $max;
}

function build_menu ( $menu )
{
  global $splice;
	$out = "\n".'              <ul id="menu" class="menu">' . "\n";
	
	for ( $i = 1; $i <= multimax( $menu ); $i++ )
	{
		if (is_array ($menu[$i]) ) {//must be by construction but let's keep the errors home
			if ($menu[$i]['parent'] == 0 ) {//are we allowed to see this menu?
        $children = get_childs ( $menu, $i );
        if(cleanstring($splice[0])==cleanstring($menu[$i]['link']) || ($menu[$i]['text']=="Home"&&$splice[0]=="index"))
          $out .= '                <li class="menu_here menu_layer_0"><a href="' . $menu [ $i ] [ 'link' ].'"><span class="menu_icon_'.cleanstring($menu[$i]['text']).'">'.$menu[$i]['text']."</span></a>";
         else
				  $out .= '                <li class="menu_top_layer menu_layer_0"><a href="' . $menu [ $i ] [ 'link' ].'"><span class="menu_icon_'.cleanstring($menu[$i]['text']).'">'.$menu[$i]['text']."</span></a>";
				if($children) $out .= $children;
				$out .= '                </li>' . "\n";
			}
		}
		else {
			//die ( sprintf ( 'menu nr %s must be an array', $i ) );
		}
	}
	
	$out .= '              </ul>'."\n";
	return $out;

}


function get_childs ( $menu, $el_id )
{
	$has_subcats = FALSE;
	$out = '';
	$out .= "\n".'                  <ul class="menu_ul">' . "\n";
  $subs = detectmenusubs();
	for ( $i = 1; $i <= multimax( $menu ); $i++ )
	{
		if ( isset($menu[$i]['parent']) && $menu [ $i ] [ 'parent' ] == $el_id ) {//are we allowed to see this menu?
			$has_subcats = TRUE;
      if($subs[$i]>0) $children = get_childs ( $menu, $i ); else $children = false;
			$out .= '                     <li class="menu_sub"><a href="' . $menu [ $i ] [ 'link' ] . '"><i>';
			$out .= $menu [ $i ] [ 'text' ];
			$out .= '</i></a>';
		  if($children) $out .= $children;
			$out .= '                </li>' . "\n";
		}
	}
	$out .= '                  </ul>'."\n";
	return ( $has_subcats ) ? $out : FALSE;
}

function createthumbnail($originalImage,$ext,$toWidth=250,$toHeight=200){
/*
    // Get the original geometry and calculate scales
    list($width, $height) = getimagesize($originalImage.".".$ext);
    $xscale=$width/$toWidth;
    $yscale=$height/$toHeight;
    
    // Recalculate new size with default ratio
    if ($yscale>$xscale){
        $new_width = round($width * (1/$yscale));
        $new_height = round($height * (1/$yscale));
    }
    else {
        $new_width = round($width * (1/$xscale));
        $new_height = round($height * (1/$xscale));
    }

    // Resize the original image
    $imageResized = imagecreatetruecolor($new_width, $new_height);
    switch(strtolower($ext)) {
     default: return false; break;
     case "jpeg": $imageTmp = imagecreatefromjpeg($originalImage.".".$ext);  break;
     case "jpg": $imageTmp = imagecreatefromjpeg($originalImage.".".$ext); break;
     case "png": $imageTmp = imagecreatefrompng($originalImage.".".$ext); break;
     case "gif": $imageTmp = imagecreatefromgif($originalImage.".".$ext); break;
    }

    imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    switch($ext) {
     case "jpeg": imagejpeg($imageResized,$originalImage."_thumb.".$ext,93); break;
     case "jpg": imagejpeg($imageResized,$originalImage."_thumb.".$ext,93); break;
     case "png": imagepng($imageResized,$originalImage."_thumb.".$ext); break;
     case "gif": imagegif($imageResized,$originalImage."_thumb.".$ext); break;
    }
    
    @imagedestroy($imageResized);
    @imagedestroy($imageTmp);

    return true;*/
}

function pdf2txt($filename){

	$data = getFileData($filename);

	// grab objects and then grab their contents (chunks)
	$a_obj = getDataArray($data,"obj","endobj");
	foreach($a_obj as $obj){

		$a_filter = getDataArray($obj,"<<",">>");
		if (is_array($a_filter)){
			$j++;
			$a_chunks[$j]["filter"] = $a_filter[0];

			$a_data = getDataArray($obj,"stream\r\n","endstream");
			if (is_array($a_data)){
				$a_chunks[$j]["data"] = substr($a_data[0],strlen("stream\r\n"),strlen($a_data[0])-strlen("stream\r\n")-strlen("endstream"));
			}
		}
	}

	// decode the chunks
	foreach($a_chunks as $chunk){

		// look at each chunk and decide how to decode it - by looking at the contents of the filter
		$a_filter = split("/",$chunk["filter"]);
		
		if ($chunk["data"]!=""){
			// look at the filter to find out which encoding has been used			
			if (substr($chunk["filter"],"FlateDecode")!==false){
				$data =@ gzuncompress($chunk["data"]);
				if (trim($data)!=""){
					$result_data .= ps2txt($data);
				} else {
				
					//$result_data .= "x";
				}
			}
		}
	}
	
	return $result_data;
	
}


// Function    : ps2txt()
// Arguments   : $ps_data - postscript data you want to convert to plain text
// Description : Does a very basic parse of postscript data to
//               return the plain text
// Author      : Jonathan Beckett, 2005-05-02
function ps2txt($ps_data){
	$result = "";
	$a_data = getDataArray($ps_data,"[","]");
	if (is_array($a_data)){
		foreach ($a_data as $ps_text){
			$a_text = getDataArray($ps_text,"(",")");
			if (is_array($a_text)){
				foreach ($a_text as $text){
					$result .= substr($text,1,strlen($text)-2);
				}
			}
		}
	} else {
		// the data may just be in raw format (outside of [] tags)
		$a_text = getDataArray($ps_data,"(",")");
		if (is_array($a_text)){
			foreach ($a_text as $text){
				$result .= substr($text,1,strlen($text)-2);
			}
		}
	}
	return $result;
}


// Function    : getFileData()
// Arguments   : $filename - filename you want to load
// Description : Reads data from a file into a variable
//               and passes that data back
// Author      : Jonathan Beckett, 2005-05-02
function getFileData($filename){
	$handle = fopen($filename,"rb");
	$data = fread($handle, filesize($filename));
	fclose($handle);
	return $data;
}


// Function    : getDataArray()
// Arguments   : $data       - data you want to chop up
//               $start_word - delimiting characters at start of each chunk
//               $end_word   - delimiting characters at end of each chunk
// Description : Loop through an array of data and put all chunks
//               between start_word and end_word in an array
// Author      : Jonathan Beckett, 2005-05-02
function getDataArray($data,$start_word,$end_word){

	$start = 0;
	$end = 0;
	unset($a_result);
	
	while ($start!==false && $end!==false){
		$start = strpos($data,$start_word,$end);
		if ($start!==false){
			$end = strpos($data,$end_word,$start);
			if ($end!==false){
				// data is between start and end
				$a_result[] = substr($data,$start,$end-$start+strlen($end_word));
			}
		}
	}
	return $a_result;
}




//ReturnSearchPDF() 
//by Billy Richardson
function returnsearchpdf($terms, $curdir='/') {
global $result;
$dir = './files';
if ($handle = opendir($dir.$curdir)) {
    while (false !== ($file = readdir($handle))) {
        print $dir.$curdir;
        if(is_dir($dir.$file)) {
          returnsearchpdf($terms,$file."/");
        }
        continue;
        if($file == "cache" || $file == "." || $file == "..") continue;
        $ofile = $file;
        $file = $curdir.$file;
        $ext = end(explode('.', $file));
        if ($ext == "pdf" && !is_dir($dir.$file)) {
           $found = array();
           $count = $loop = 0;
           $header = $returnline = "";
            $GetFile = pdf2txt($dir.$file);
            print $dir.$file."<br>";
            if($GetFile) {
                  if(preg_match("/".$temp."/is",$GetFile)) {
                  $found[$d] = true;
                  $returnline = $GetFile;
                  $count++;
                  }
     
             }
          $all = false;
          if($returnline == "") $returnline = $firstline;
          if($header[1]) $header = $header[1]; else $header = titlestring(str_replace(".html","",$ofile));
          foreach($terms as $d => $temp) { if(preg_match("/".$temp."/is",$header)) { $count+=100; $all = true; } elseif(preg_match("/".$temp."/is",$file)) { $count+=50; $all = true; } elseif(isset($found[$d])) $all = true; else { $all = false; break; } }
          for($k=0;$k<=strlen($file);$k++) {
            $count-= 1;
          }
          if($all) $result[] = array($count,$file,$header,trim($returnline));
        } elseif(is_dir($dir.$file)) {
          returnsearchpdf($terms,$file."/");
        }
    }
    closedir($handle);
}
return $result;
}


function return_page($filename) {
  $curl = curl_init();
  curl_setopt($curl , CURLOPT_URL , $filename);
  curl_setopt($curl , CURLOPT_RETURNTRANSFER , 1);
  curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT ".mt_rand(3,7).".".mt_rand(0,9)."; en-US; rv:1.8.1.1) Gecko/".mt_rand(11111111,99999999)." Firefox/".mt_rand(1,4).".0.0.".mt_rand(0,9));
  curl_setopt($curl , CURLOPT_VERBOSE , 1);
  curl_setopt($curl , CURLOPT_HEADER , 0);
  $file =  curl_exec($curl);
  curl_close($curl);
  return $file;
}