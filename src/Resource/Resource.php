<?php

namespace Drupal\ip_lookup\Resource;
use Drupal\Core\Database\Connection;
use Drupal\ip_lookup\Resource\Environment;
use Drupal\ip_lookup\Resource\ClientIp;

class Resource {
  
  /**
   * User IP
   *
   * @var \Drupal\ip_lookup\Resource
   */
   protected $ip;
  
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  
  protected $connection;

  /**
   * Constructs resource object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   A database connection for reading ip_lookup tabel.
   */
  public function __construct(Connection $connection, ClientIp $ip ) {
    $this->connection  = $connection;
    $ip = $ip->get_ip();
    $this->ip = $ip;
  } 

  public function get_location() {
    // Get the config object
    $key = 'test';
    $config = \Drupal::config('ipApikey.settings');
    if (!$config->isNew()) {
      // Get the key value
      $key = $config->get('api_key');
    }
    $base = Environment::getUrlStem();
    $url = $base . $this->ip . '?' . "api-key=$key";
    $details = json_decode(file_get_contents($url));
    
    $output = [];
    $output['ip'] = $details->ip;
    $output['city'] = $details->city;
    $output['region'] = $details->region;
    return $output;
  }  

  public function get_location_query() {
    $query = $this->connection->select('ip_lookup', 'm')
      ->fields('m')
      ->condition('m.ip', $this->ip, '=');

    $resource = $query->execute();
    $result = $resource->fetchAssoc();
    return $result; 
  }
}
