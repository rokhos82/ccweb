<?php
//header("Content-Type: text/plain");
if (!function_exists('file_get_contents')) {
  function file_get_contents($filename) {

    $fhandle = fopen($filename, "r");
    $fcontents = fread($fhandle, filesize($filename));
    fclose($fhandle);

    return $fcontents;
   }
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{
  $postData = $_POST;
  $temp = array(0,0,0);
  
  foreach($postData as $temp[0] => $temp[1])
    $temp[2] .= "&".urlencode($temp[0])."=".urlencode($temp[1]);
  
  $hash = md5($temp[2]);
  $file = "./cache/".$hash.".json";

  if(file_exists($file) /*&& time()-filemtime($file) < 604800*/)
  {
    echo @file_get_contents($file);
  }
  else
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.wyomingextension.org/converse4h/wp-admin/admin-ajax.php");
    curl_setopt($ch, CURLOPT_REFERER,  "http://www.wyomingextension.org/converse4h/?page_id=29");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT ".mt_rand(4,6).".".mt_rand(0,2)."; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/".mt_rand(2,4).".".mt_rand(0,6).".".mt_rand(10,15));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $temp[2] );
    
    $ret = curl_exec( $ch );
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo $ret;
    
		//@file_put_contents("./last.txt",var_dump(json_decode($ret)));
    //if(intval($httpcode) == 200)
    @file_put_contents($file,$ret);
    
    curl_close ( $ch );
  }
}
