<?php

namespace Drupal\ip_lookup\Resource;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Database\Connection;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Ipdata Resource.
 */
class Resource {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs resource object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   A database connection for reading ip_lookup tabel.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle client object.
   */
  public function __construct(Connection $connection, RequestStack $request_stack, ConfigFactoryInterface $config_factory, ClientInterface $http_client) {
    $this->connection = $connection;
    $this->requestStack = $request_stack;
    $this->configFactory = $config_factory;
    $this->httpClient = $http_client;
  }

  /**
   * User ipdata endpoint to get user Location.
   */
  public function getLocation() {
    // Get user IP.
    $ip = $this->getIp();

    // Get the config object.
    $key = 'test';
    $config = $this->configFactory->get('ipApikey.settings');
    if (!$config->isNew() && !empty($config->get('api_key'))) {
      // Get the key value.
      $key = $config->get('api_key');
    }
    $base = Environment::getUrlStem();
    $url = $base . $ip . '?' . "api-key=$key";
    $details = json_decode($this->fetchProjectData($url));

    $output = [];
    $output['ip'] = $details->ip;
    $output['city'] = $details->city;
    $output['region'] = $details->region;
    return $output;
  }

  /**
   * Using http Client to fetch data.
   */
  public function fetchProjectData($url) {
    $method = 'GET';
    try {
      $response = $this->httpClient->request($method, $url);
      $code = $response->getStatusCode();
      if ($code == 200) {
        $body = $response->getBody()->getContents();
        return $body;
      }
    }
    catch(RequestException $e) {
      watchdog_exception('ip_lookup', $e);
    }
  }

  /**
   * Query ip_lookup table with user IP.
   */
  public function getLocationQuery() {
    $ip = $this->getIp();
    $query = $this->connection->select('ip_lookup', 'm')
      ->fields('m')
      ->condition('m.ip', $ip, '=');

    $resource = $query->execute();
    $result = $resource->fetchAssoc();
    return $result;
  }

  /**
   * Get User IP.
   */
  protected function getIp() {
    $request = $this->requestStack->getCurrentRequest();
    $ip = $request->getClientIp();
    return $ip;
  }

}
