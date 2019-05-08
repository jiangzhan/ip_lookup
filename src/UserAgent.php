<?php

namespace Drupal\member_login;


class UserAgent {
  
  protected $u_agent;
  protected $bname;
  protected $platform;
  protected $version;

  function __construct ($u_agent, $bname = 'Unknown', $platform = 'Unknown', $version= " ") {
    $this->u_agent = $u_agent;
    $this->bname = $bname;      
    $this->platform = $platform;
    $this->version = $version;
  }

  public function getBrowser() { 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 

    //First get the platform?
    if (preg_match('/linux/i', $this->u_agent)) {
      $this->platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $this->u_agent)) {
      $this->platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $this->u_agent)) {
      $this->platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i',$this->u_agent) && !preg_match('/Opera/i',$this->u_agent)) { 
      $this->bname = 'Internet Explorer'; 
      $ub = "MSIE"; 
    } 
    elseif (preg_match('/Firefox/i',$this->u_agent)) { 
      $this->bname = 'Mozilla Firefox'; 
      $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$this->u_agent)) { 
      $this->bname = 'Google Chrome'; 
      $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$this->u_agent)) { 
      $this->bname = 'Apple Safari'; 
      $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$this->u_agent)) { 
      $this->bname = 'Opera'; 
      $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$this->u_agent)) { 
      $this->bname = 'Netscape'; 
      $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
      //we will have two since we are not using 'other' argument yet
      //see if version is before or after the name
      if (strripos($this->u_agent,"Version") < strripos($this->u_agent, $ub)){
        $this->version= $matches['version'][0];
      }
      else {
        $this->version= $matches['version'][1];
      }
    }
    else {
      $this->version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($this->version==null || $this->version=="") {$this->version="?";}
    
    return array(
      'userAgent' => $this->u_agent,
      'name'      => $this->bname,
      'version'   => $this->version,
      'platform'  => $this->platform,
    );
  } 
}
