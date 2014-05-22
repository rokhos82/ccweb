<?php
//Google JSON site search
//Billy Richardson
//ConverseCounty.org
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

function curl_request($url, $postData = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/".mt_rand(2,4).".0.0.".mt_rand(0,9));
    if($postData !== false)
    {
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); 
    //if($httpCode == 200 || $httpCode == 301 || $httpCode == 302)
    return $result;
   // else
     // return false;
}

if (!function_exists('json_decode')) {
    function json_decode($content, $assoc=false) {
        require_once 'include/json.php';
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        else {
            $json = new Services_JSON;
        }
        return $json->decode($content);
    }
}

if (!function_exists('json_encode')) {
    function json_encode($content) {
        require_once 'include/json.php';
        $json = new Services_JSON;
        return $json->encode($content);
    }
}
//preg_match_all('/<c o="([^"]*)" l="([^"]*)" s="([^"]*)">([^<]*)<\/c>/', $xml_response, $matches, PREG_SET_ORDER);
$query = 'elecctiion';

$results = curl_request('http://ajax.googleapis.com/ajax/services/search/web?rsz=large&v=1.0&q='.urlencode($query).'+site:conversecounty.org');

$searcherror = true;

if($results)
{
  $results = json_decode($results);
  if($results->responseStatus == 200)
  {
    $searcherror = false;
    if(count($results->responseData->results) <= 0)
    {
        print "spelling: ";
        require "./spellcheck/include.php"   ;
        
        function suggestionslink ($input){
        	if((trim($input))==""){return "";}
        
        	$spellcheckObject = new PHPSpellCheck();
        	$spellcheckObject -> DictionaryPath = ("./spellcheck/dictionaries/");
        	$spellcheckObject -> LoadDictionary("English (International)") ;
            $suggestionText = $spellcheckObject ->didYouMean($input);
        
        	if($suggestionText==""){return "";}
        
        	return "<a href='".$_SERVER["SCRIPT_NAME"]."?searchBox=".htmlentities($suggestionText)."'>".$suggestionText."</a>";
        
        }
        
        die($suggestionslink($query)."lol");
    }
    else
    {
      foreach($results->responseData->results as $temp)
      {
        print_r($temp);
      }
    }
  }
}

if($searcherror)
{
  die('use site search');
}