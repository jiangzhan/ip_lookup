<?php

namespace Drupal\member_login\Resource;

class Environment{
  protected static $url_stem = "https://api.ipdata.co";
  public static function getUrlStem() {
    return self::$url_stem . '/';  
  }
}
