<?php

namespace Drupal\ip_lookup\Resource;

/**
 * base environment class  
 */

class Environment {
  protected static $urlStem = "https://api.ipdata.co";
  /**
   *  get base url;
   */
  public static function getUrlStem() {
    return self::$urlStem . '/'; 
  }

}
