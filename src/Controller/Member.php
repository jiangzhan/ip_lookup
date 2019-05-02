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
   * @para Drupal\Core\Session\AccountProxy $currentUser
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
    return new static(
      $container->get('current_user'),
      $container->get('database')
    );
  }
  
  /**
   * A simple controller method to print member login table.
   */

  public function list() {
    // We are going to output the results in a table with a nice header. 
    $header = array(
      array('data' => 'Account Name', 'field' => 'username'),
      array('data' => 'Uid', 'field' => 'uid'),
      array('data' => 'Time', 'field' => 'date', 'sort' => 'desc'),
    );
    $data = $this->member_get_result($header);
    $rows = [];
    foreach($data AS $value) {
      $class = $this->currentUser->id() == $value->uid ? 'current-member-login' : ''; 
      $rows[] = array(
        'data' => array(
          $value->username, $value->uid, date("F j, Y, g:i a",$value->date)
        ), 
        'class' => array($class),
      );
    }

    $output = [];
    $output[] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    );
    $output[] = array('#type' => 'pager');
    $output[] = array(
      '#attached' => array('library' => array('member_login/member_login')),
    );
    return $output;
  }
  
  public function member_get_result($header = array()) {

    $query = $this->connection->select('member_login', 'm')
      ->extend('\Drupal\Core\Database\Query\PagerSelectExtender')
      ->extend('\Drupal\Core\Database\Query\TableSortExtender');
    $query = $query
      ->fields('m', array())
      ->limit(30)
      ->orderByHeader($header);

    $resource = $query->execute();
    $result = $resource->fetchAll();
    return $result;
  }
}
