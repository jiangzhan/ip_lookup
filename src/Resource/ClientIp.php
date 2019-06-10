<?php

namespace Drupal\ip_lookup\Resource;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Get Client IP.
 */
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
  /**
   * Get User IP.
   */
  public function getIp() {
    $ip = $this->request->getClientIp();
    return $ip;    
  }

}
