<?php

namespace Drupal\member_login\Resource;
use Drupal\Core\Database\Connection;
use Drupal\member_login\Resource\Environment;
use Drupal\member_login\Resource\ClientIp;

class Resource {
  
  /**
   * User IP
   *
   * @var \Drupal\member_login\Resource
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
   *   A database connection for reading member_lgoin tabel.
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
    $query = $this->connection->select('member_login', 'm')
      ->fields('m')
      ->condition('m.ip', $this->ip, '=');

    $resource = $query->execute();
    $result = $resource->fetchAssoc();
    return $result; 
  }
}
