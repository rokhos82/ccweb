<?php 
$expires = 60*60*24*14;
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
header("Content-type: image/gif");

if(isset($_GET['type'])) $type = str_replace("_","",$_GET['type']); else $type = "";

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
}

$text = stripslashes(str_replace("_"," ",$_GET['text']));
if(empty($text)) $text = "ERROR";

$cache = '/im/cache/titles/'.md5($type.$text).'.gif';
if(file_exists('..'.$cache)) {
  header("Location: ".$cache);
  die();
}

function calculateTextBox($font_size, $font_angle, $font_file, $text) {
  $box   = imagettfbbox($font_size, $font_angle, $font_file, $text);
  if( !$box )
    return false;
  $min_x = min( array($box[0], $box[2], $box[4], $box[6]) );
  $max_x = max( array($box[0], $box[2], $box[4], $box[6]) );
  $min_y = min( array($box[1], $box[3], $box[5], $box[7]) );
  $max_y = max( array($box[1], $box[3], $box[5], $box[7]) );
  $width  = ( $max_x - $min_x );
  $height = ( $max_y - $min_y );
  $left   = abs( $min_x ) + $width;
  $top    = abs( $min_y ) + $height;
  // to calculate the exact bounding box i write the text in a large image
  $img     = @imagecreatetruecolor( $width << 2, $height << 2 );
  $white   =  imagecolorallocate( $img, 255, 255, 255 );
  $black   =  imagecolorallocate( $img, 0, 0, 0 );
  imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
  // for sure the text is completely in the image!
  imagettftext( $img, $font_size,
                $font_angle, $left, $top,
                $white, $font_file, $text);
  // start scanning (0=> black => empty)
  $rleft  = $w4 = $width<<2;
  $rright = 0;
  $rbottom   = 0;
  $rtop = $h4 = $height<<2;
  for( $x = 0; $x < $w4; $x++ )
    for( $y = 0; $y < $h4; $y++ )
      if( imagecolorat( $img, $x, $y ) ){
        $rleft   = min( $rleft, $x );
        $rright  = max( $rright, $x );
        $rtop    = min( $rtop, $y );
        $rbottom = max( $rbottom, $y );
      }
  // destroy img and serve the result
  imagedestroy( $img );
  return array( "left"   => $left - $rleft,
                "top"    => $top  - $rtop,
                "width"  => $rright - $rleft + 1,
                "height" => $rbottom - $rtop + 1 );
} 


$fontsize = 24;


$width = 500;
$height = 37;
$top = 24;
$font = './expressway.ttf';

switch($type) {
 default:
	$q = calculateTextBox($fontsize,0,$font,$text);
  $width = $q['width']+2;
  $height = $q['height']+2;
  $img = @imagecreate($width, $height);
	$txtcolor = imagecolorallocate($img, 41, 41, 41);
  $back = imagecolorallocate($img, 255, 255, 255); 
 break;
 case "sub":
  $font = './Antipasto.ttf';
  $fontsize = 22;
  $top = 18;
	$q = calculateTextBox($fontsize,0,$font,$text);
  $width = $q['width']+2;
  $height = $q['height']+2;
  $img = @imagecreate($width, $height);
	$txtcolor = imagecolorallocate($img, 66, 66, 66);
  $back = imagecolorallocate($img, 255, 255, 255); 
 break;
 case "dep":
  $font = './Antipasto.ttf';
  $fontsize = 22;
  $top = 18;
	$q = calculateTextBox($fontsize,0,$font,$text);
  $width = $q['width']+2;
  $height = $q['height']+2;
  $img = @imagecreate($width, $height);
	$txtcolor = imagecolorallocate($img, 0,0,0);
  $back = imagecolorallocate($img, 255, 255, 255); 
 break;
}

imageFilledRectangle($img, 0, 0, $width - 1 , $height - 1, $back);
imagettftext($img, $fontsize, 0, 1, $top, $txtcolor, $font, $text);
imagecolortransparent($img, $back); 
imagegif($img,'..'.$cache);
imagegif($img);
imagedestroy($img);

 ?>