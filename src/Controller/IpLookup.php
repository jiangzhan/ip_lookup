<?php

namespace Drupal\ip_lookup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;

/**
 * Provides the User IP Lookup page.
 */
class IpLookup extends ControllerBase {

  /**
   * The current user.
   *
   * @var Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a current user object.
   *
   * @param Drupal\Core\Session\AccountProxy $current_user
   *   A current user.
   * @param \Drupal\Core\Database\Connection $connection
   *   A database connection for reading ip_lookup tabel.
   */
  public function __construct(AccountProxy $current_user, Connection $connection) {
    $this->currentUser = $current_user;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $controller = new static(
      $container->get('current_user'),
      $container->get('database')
    );
    $controller->setStringTranslation($container->get('string_translation'));
    return $controller;
  }

  /**
   * A simple controller method to print user ip_lookup table.
   */
  public function list() {
    $header = [
      ['data' => $this->t('Account Name'), 'field' => 'm.username'],
      ['data' => $this->t('Uid'), 'field' => 'm.uid'],
      ['data' => $this->t('Time'), 'field' => 'm.date', 'sort' => 'desc'],
      ['data' => $this->t('Browser Name'), 'field' => 'm.browser_name'],
      ['data' => $this->t('Browser Version'), 'field' => 'm.browser_version'],
      ['data' => $this->t('Browser Platform'), 'field' => 'm.browser_platform'],
      ['data' => $this->t('Ip'), 'field' => 'm.ip'],
      ['data' => $this->t('City'), 'field' => 'm.city'],
      ['data' => $this->t('Region'), 'field' => 'm.region'],
    ];
    $data = $this->ipLookupGetResult($header);

    $rows = [];
    foreach ($data as $value) {
      $class = $this->currentUser->id() == $value->uid ? 'current-user-ip-lookup' : '';
      $rows[] = [
        'data' => [
          $value->username,
          $value->uid,
          date("F j, Y, g:i a", $value->date),
          $value->browser_name,
          $value->browser_version,
          $value->browser_platform,
          $value->ip,
          $value->city,
          $value->region,
        ],
        'class' => [$class],
      ];
    }

    $build = [];
    $build['ip_lookup'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => ['class' => ['ip-lookup-table']],
      '#attached' => ['library' => ['ip_lookup/ip_lookup']],
    ];

    $build[] = ['#type' => 'pager'];

    return $build;
  }

  /**
   * Query the table.
   *
   * @param array $header
   *   The table header.
   */
  public function ipLookupGetResult(array $header = []) {
    $query = $this->connection->select('ip_lookup', 'm')
      ->extend('\Drupal\Core\Database\Query\PagerSelectExtender')
      ->extend('\Drupal\Core\Database\Query\TableSortExtender');
    $query = $query
      ->fields('m')
      ->limit(10)
      ->orderByHeader($header);

    $resource = $query->execute();
    $result = $resource->fetchAll();
    return $result;
  }

}
