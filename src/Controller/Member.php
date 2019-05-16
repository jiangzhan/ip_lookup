<?php

namespace Drupal\member_login\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Session\AccountProxy;

/**
 * Provides the member login page.
 */

class Member extends ControllerBase {
  
  /**
   * The current user.
   *
   * @var Drupal\Core\Session\AccountProxy;
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
   * @param Drupal\Core\Session\AccountProxy $currentUser
   *   A current user.
   * @param \Drupal\Core\Database\Connection $connection
   *   A database connection for reading member_lgoin tabel.
   */
  public function __construct( AccountProxy $currentUser, Connection $connection ) {
    $this->currentUser = $currentUser;
    $this->connection  = $connection;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create( ContainerInterface $container ) {
    $controller = new static(
      $container->get('current_user'),
      $container->get('database')
    );
    $controller->setStringTranslation($container->get('string_translation'));
    return $controller;
  }
  
  /**
   * A simple controller method to print member login table.
   */
  public function list() {
    $header = [
      ['data' => $this->t('Account Name'), 'field' => 'm.username'],
      ['data' => $this->t('Uid'), 'field' => 'm.uid'],
      ['data' => $this->t('Time'), 'field' => 'm.date', 'sort' => 'desc'],
      ['data' => $this->t('Browser Name'), 'field' => 'm.browser_name'],
      ['data' => $this->t('Browser Version'), 'field' => 'm.browser_version'],
      ['data' => $this->t('Browser Platform'), 'field' => 'm.browser_platform'],
    ];
    $data = $this->member_get_result($header);

    $rows = [];
    foreach($data AS $value) {
      $class = $this->currentUser->id() == $value->uid ? 'current-member-login' : ''; 
      $rows[] = [
        'data' => [
          $value->username, 
          $value->uid, 
          date("F j, Y, g:i a", $value->date),
          $value->browser_name,
          $value->browser_version,
          $value->browser_platform,
        ], 
        'class' => [$class],
      ];
    }

    $build = [];
    $build['member_login'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attached' => ['library' => ['member_login/member_login']],
    ];

    $build[] = ['#type' => 'pager'];

    return $build;
  }

  /**
   * Query the table.
   *
   * @param array $header
   * The table header.
   */ 
  public function member_get_result($header = []) {
    $query = $this->connection->select('member_login', 'm')
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
