<?php
namespace Drupal\member_login\Resource;

use Symfony\Component\HttpFoundation\RequestStack;

class ClientIp {
  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a ClientIp object.
   *
   * @param Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack object.
   */
   public function __construct(RequestStack $request_stack) {
    $this->request = $request_stack->getCurrentRequest();
  }
  public function get_ip() {
    $ip = $this->request->getClientIp();
    return $ip;    
  }
}
