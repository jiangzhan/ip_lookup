<?php

namespace Drupal\ip_lookup\Resource;

/**
 * Base environment class.
 */
class Environment {
  protected static $urlStem = "https://api.ipdata.co";
  /**
   * Get base url.
   */
  public static function getUrlStem() {
    return self::$urlStem . '/';
  }

}
