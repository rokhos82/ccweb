<?php

require('includes/functions.php');

function returnpdf($terms, $curdir='/') {
global $result;
$dir = './files';
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

?>