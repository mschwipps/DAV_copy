#!/usr/local/bin/php
<?php
/**
 * A Tool for uploading/PUTting some files to a WebDAV-Server.
 *
 * Param: 1. destination-Directory
 *        2.-n. Files to upload
 *
 * The WebDAVClient-class is based on awl used by Davical.
 * http://www.davical.org
 * author Andrew McMillan <debian@mcmillan.net.nz>
 * changed 2009 by Andres Obrero - Switzerland <andres@obrero.ch>
 *
 * changed 2014 by Michael Schwipps <msc@msc-data.de>
 *
 * @license   http://gnu.org/copyleft/gpl.html GNU GPL v2
 */

$server = "https://example.com";
$user = "username";
$passwort = "secret";
$subdir = $argv[1];


$max = count($argv);
$RET=0;
$client = new WebDAVClient( $server, $user, $passwort );

class WebDAVClient {

  private $base_url, $user, $pass, $protocol, $server, $port;

  private $httpRequest = ""; // for debugging http headers sent
  private $httpResponse = ""; // for debugging http headers received
  private $status = ""; // return-Code/Status
  private $error_msg = "";

  /**
  * Constructor, initialises the class
  *
  * @param string $base_url  The URL for the  server
  * @param string $user      The name of the user logging in
  * @param string $pass      The password for that user
  */
  function WebDAVClient( $base_url, $user, $pass ) {
    $this->user = $user;
    $this->pass = $pass;

    if ( preg_match( '#^(https?)://([a-z0-9_.-]+)(:([0-9]+))?(/.*)$#', $base_url, $matches ) ) {
      $this->server = $matches[2];
      $this->base_url = $matches[5];
      if ( $matches[1] == 'https' ) {
        $this->protocol = 'ssl';
        $this->port = 443;
      }
      else {
        $this->protocol = 'tcp';
        $this->port = 80;
      }
      if ( $matches[4] != '' ) {
        $this->port = intval($matches[4]);
      }
    }
    else {
      trigger_error("Invalid URL: '".$base_url."'", E_USER_ERROR);
    }
  }

  /**
   * Output http request headers
   *
   * @return HTTP headers
   */
  function GetHttpRequest() {
      return $this->httpRequest;
  }

  /**
   * Output http response headers
   *
   * @return HTTP headers
   */
  function GetHttpResponse() {
      return $this->httpResponse;
  }

  /**
   * Output PUT-error-messages.
   */
  function GetErrorMsg() {
      return $this->error_msg;
  }

  /**
   * checks if upload was ok
   */
  function isOk() {
      return (200 <= $this->status && $this->status < 300);
  }

  /**
  * PUT a binary file.
  * Send a request to the server.
  *
  * @param string $relative_url The URL to make the request to, relative to $base_url
  * @param resource $content The file to upload.
  * @param int $content_length The lenth of $content.
  *
  * @return string The content of the response from the server or FALSE.
  *    
  */
  function put( $relative_url, $content, $content_length ) {

    $this->httpResponse = "";
    $this->error_msg = "";
    $this->status = 0;

    if(!defined("_FSOCK_TIMEOUT")){ define("_FSOCK_TIMEOUT", 10); }
    $headers = array();

    $headers[] = "PUT ". $this->base_url . $relative_url . " HTTP/1.1";
    $headers[] = "Authorization: Basic ".base64_encode($this->user .":". $this->pass );
    $headers[] = "Host: ".$this->server .":".$this->port;
    $headers[] = "Content-type: application/octet-stream";

    $headers[] = "Content-Length: " . $content_length;
    $headers[] = 'Connection: close';
    $this->httpRequest = join("\r\n",$headers);


    $fip = fsockopen( $this->protocol . '://' . $this->server, $this->port, $errno, $errstr, _FSOCK_TIMEOUT); //error handling?
    if (FALSE == $fip) {
        fclose($fip);
        $this->error_msg = "fsockopen: $relative_url - $errno $errstr";
        return FALSE; 
    }
    if ( !(get_resource_type($fip) == 'stream') ) {
        fclose($fip);
        $this->error_msg = "get_resource_type-error: $relative_url ";
        return FALSE;
    }
    if ( !fwrite($fip, $this->httpRequest."\r\n\r\n") ) { 
        fclose($fip);
        $this->error_msg = "fwrite-error: Request $relative_url ";
        return FALSE; 
    }

    while ($s = fread($content, 1048576) ) { 
        if ( !fwrite($fip, $s) ) { 
            fclose($fip);
            $this->error_msg = "fwrite-error: Body $relative_url ";
            return FALSE; 
        }
    }
    fclose($content); 

    $rsp = "";
    if( !feof($fip) ) { 
        $zeile =fgets($fip,8192); 
        $a = explode(" ", $zeile, 3);
        $this->status = $a[1];
        $rsp .=  $zeile;
    }

    while( !feof($fip) ) { $rsp .= fgets($fip,8192); }
    fclose($fip);
    $this->httpResponse = trim($rsp);
    return $this->httpResponse;
  }
}

# main function
for($i=2; $i < $max; $i++) {

    $filename = $argv[$i];
    if ($content = fopen($filename, 'r')) {
        $content_length = filesize($filename);

        $respons = $client->put("$subdir"."$filename", $content, $content_length);
        if (! $client->isOK() || !$respons) {
            echo "$respons\n".$client->GetErrorMsg()."\n";
            $RET=1;
        }
    }
}

exit($RET); 
?>
