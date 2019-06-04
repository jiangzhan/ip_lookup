<?php

namespace Drupal\ip_lookup\Resource;

class Environment{
  protected static $url_stem = "https://api.ipdata.co";
  public static function getUrlStem() {
    return self::$url_stem . '/';  
  }
}
