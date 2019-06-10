<?php

namespace Drupal\ip_lookup\Resource;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;

/**
 * Ipdata Resource.
 */
class Resource {

  /**
   * User IP.
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
   * @param \Drupal\ip_lookup\Resource\ClientIp $ip
   *   Symfony Request::getClientIP.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(Connection $connection, ClientIp $ip, ConfigFactoryInterface $config_factory) {
    $this->connection = $connection;
    $this->ip = $ip->getIp();
    $this->config = $config_factory;
  }

  /**
   * User ipdata endpoint to get user Location.
   */
  public function getLocation() {
    // Get the config object.
    $key = 'test';
    $config = $this->config->get('ipApikey.settings');
    if (!$config->isNew() && !empty($config->get('api_key'))) {
      // Get the key value.
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

  /**
   * Query ip_lookup table with user IP.
   */
  public function getLocationQuery() {
    $query = $this->connection->select('ip_lookup', 'm')
      ->fields('m')
      ->condition('m.ip', $this->ip, '=');

    $resource = $query->execute();
    $result = $resource->fetchAssoc();
    return $result;
  }

}
