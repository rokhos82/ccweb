<?php
/**
 * Utilities for handling HTTP related tasks
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 */

define('HTTP_MULTIPART_BOUNDARY','D0KuW1K1B0uNDARY');
define('HTTP_HEADER_LF',"\r\n");
define('HTTP_CHUNK_SIZE',16*1024);

/**
 * Checks and sets HTTP headers for conditional HTTP requests
 *
 * @author   Simon Willison <swillison@gmail.com>
 * @link     http://simonwillison.net/2003/Apr/23/conditionalGet/
 * @param    timestamp $timestamp lastmodified time of the cache file
 * @returns  void or exits with previously header() commands executed
 */
function http_conditionalRequest($timestamp){
    // A PHP implementation of conditional get, see
    //   http://fishbowl.pastiche.org/2002/10/21/http_conditional_get_for_rss_hackers/
    $last_modified = substr(gmdate('r', $timestamp), 0, -5).'GMT';
    $etag = '"'.md5($last_modified).'"';
    // Send the headers
    header("Last-Modified: $last_modified");
    header("ETag: $etag");
    // See if the client has provided the required headers
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
        $if_modified_since = stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    }else{
        $if_modified_since = false;
    }

    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])){
        $if_none_match = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
    }else{
        $if_none_match = false;
    }

    if (!$if_modified_since && !$if_none_match){
        return;
    }

    // At least one of the headers is there - check them
    if ($if_none_match && $if_none_match != $etag) {
        return; // etag is there but doesn't match
    }

    if ($if_modified_since && $if_modified_since != $last_modified) {
        return; // if-modified-since is there but doesn't match
    }

    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.0 304 Not Modified');

    // don't produce output, even if compression is on
    @ob_end_clean();
    exit;
}

/**
 * Let the webserver send the given file vi x-sendfile method
 *
 * @author Chris Smith <chris.eureka@jalakai.co.uk>
 * @returns  void or exits with previously header() commands executed
 */
function http_sendfile($file) {
    global $conf;

    //use x-sendfile header to pass the delivery to compatible webservers
    if($conf['xsendfile'] == 1){
        header("X-LIGHTTPD-send-file: $file");
        ob_end_clean();
        exit;
    }elseif($conf['xsendfile'] == 2){
        header("X-Sendfile: $file");
        ob_end_clean();
        exit;
    }elseif($conf['xsendfile'] == 3){
        header("X-Accel-Redirect: $file");
        ob_end_clean();
        exit;
    }

    return false;
}

/**
 * Send file contents supporting rangeRequests
 *
 * This function exits the running script
 *
 * @param ressource $fh - file handle for an already open file
 * @param int $size     - size of the whole file
 * @param int $mime     - MIME type of the file
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 */
function http_rangeRequest($fh,$size,$mime){
    $ranges  = array();
    $isrange = false;

    header('Accept-Ranges: bytes');

    if(!isset($_SERVER['HTTP_RANGE'])){
        // no range requested - send the whole file
        $ranges[] = array(0,$size,$size);
    }else{
        $t = explode('=', $_SERVER['HTTP_RANGE']);
        if (!$t[0]=='bytes') {
            // we only understand byte ranges - send the whole file
            $ranges[] = array(0,$size,$size);
        }else{
            $isrange = true;
            // handle multiple ranges
            $r = explode(',',$t[1]);
            foreach($r as $x){
                $p = explode('-', $x);
                $start = (int)$p[0];
                $end   = (int)$p[1];
                if (!$end) $end = $size - 1;
                if ($start > $end || $start > $size || $end > $size){
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    print 'Bad Range Request!';
                    exit;
                }
                $len = $end - $start + 1;
                $ranges[] = array($start,$end,$len);
            }
        }
    }
    $parts = count($ranges);

    // now send the type and length headers
    if(!$isrange){
        header("Content-Type: $mime",true);
    }else{
        header('HTTP/1.1 206 Partial Content');
        if($parts == 1){
            header("Content-Type: $mime",true);
        }else{
            header('Content-Type: multipart/byteranges; boundary='.HTTP_MULTIPART_BOUNDARY,true);
        }
    }

    // send all ranges
    for($i=0; $i<$parts; $i++){
        list($start,$end,$len) = $ranges[$i];

        // multipart or normal headers
        if($parts > 1){
            echo HTTP_HEADER_LF.'--'.HTTP_MULTIPART_BOUNDARY.HTTP_HEADER_LF;
            echo "Content-Type: $mime".HTTP_HEADER_LF;
            echo "Content-Range: bytes $start-$end/$size".HTTP_HEADER_LF;
            echo HTTP_HEADER_LF;
        }else{
            header("Content-Length: $len");
            if($isrange){
                header("Content-Range: bytes $start-$end/$size");
            }
        }

        // send file content
        fseek($fh,$start); //seek to start of range
        $chunk = ($len > HTTP_CHUNK_SIZE) ? HTTP_CHUNK_SIZE : $len;
        while (!feof($fh) && $chunk > 0) {
            @set_time_limit(30); // large files can take a lot of time
            print fread($fh, $chunk);
            flush();
            $len -= $chunk;
            $chunk = ($len > HTTP_CHUNK_SIZE) ? HTTP_CHUNK_SIZE : $len;
        }
    }
    if($parts > 1){
        echo HTTP_HEADER_LF.'--'.HTTP_MULTIPART_BOUNDARY.'--'.HTTP_HEADER_LF;
    }

    // everything should be done here, exit
    exit;
}

/**
 * Check for a gzipped version and create if necessary
 *
 * return true if there exists a gzip version of the uncompressed file
 * (samepath/samefilename.sameext.gz) created after the uncompressed file
 *
 * @author Chris Smith <chris.eureka@jalakai.co.uk>
 */
function http_gzip_valid($uncompressed_file) {
    $gzip = $uncompressed_file.'.gz';
    if (filemtime($gzip) < filemtime($uncompressed_file)) {    // filemtime returns false (0) if file doesn't exist
        return copy($uncompressed_file, 'compress.zlib://'.$gzip);
    }

    return true;
}
