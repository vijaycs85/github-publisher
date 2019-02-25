<?php

/**
 * Repository.
 */

namespace Vijaycs85\GithubPublisher;

/**
 * Class Repository
 *
 * @package Vijaycs85\GithubPublisher
 */
class Repository {

  /**
   * @var
   */
  protected $name;

  /**
   * @var null
   */
  protected $token;

  /**
   * Repository constructor.
   *
   * @param $name
   * @param null $token
   */
  public function __construct($name, $token = NULL) {
    $this->name = $name;
    $this->token = $token;
  }

  /**
   * @return null|string
   */
  protected function getAuthentication() {
    return$this->token ? $this->token . ':x-oauth-basic@' : NULL;
  }

  /**
   * @return string
   */
  public function getUrl() {
    return sprintf('https://%sgithub.com/%s.git', $this->getAuthentication(), $this->name);
  }

  /**
   * @return mixed
   */
  public function getDirectoryName() {
    return str_replace('/', '-', $this->name);
  }

}
