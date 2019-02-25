<?php

/**
 * Builder.
 */

namespace Vijaycs85\GithubPublisher;

use DateTime;
use DateTimeZone;

/**
 * Class Builder
 *
 * @package Vijaycs85\GithubPublisher
 */
class Builder {

  /**
   * @var string
   */
  protected $buildDirectory;

  /**
   * @var \Vijaycs85\GithubPublisher\Repository
   */
  protected $repository;

  /**
   * Builder constructor.
   *
   * @param $dir
   * @param \Vijaycs85\GithubPublisher\Repository $repository
   */
  public function __construct($dir, Repository $repository) {
    $this->buildDirectory = $dir;
    $this->repository = $repository;
  }

  /**
   * @param $source_path
   * @param string $target_branch
   */
  public function publish($source_path, $target_branch = 'master')  {
    $target_path = $this->getTargetPath();
    $this->cleanBuildDir();
    $this->cloneRepository();
    $this->copyGenerated($source_path, $target_path);
    $this->commit($target_path);
    $this->push($target_path, $target_branch);
  }

  /**
   * @param $path
   * @param $branch
   */
  protected function push($path, $branch) {
    $this->runInPath(
      function () use ($path, $branch) {
        $this->execute('git push origin ' . $branch);
      },
      $path
    );
  }

  /**
   * @param $path
   */
  protected function commit($path) {
    $this->runInPath(
      function () use ($path) {
        $this->execute('git add .');

        $message = sprintf('Autogenerated commit at "%s"', (new DateTime('now', new DateTimeZone('UTC')))->format(DateTime::W3C));

        $this->execute('git commit -m ' . escapeshellarg($message));
      },
      $path
    );
  }

  /**
   * @param $command
   *
   * @return mixed
   */
  protected function execute($command) {
    exec($command . ' 2>&1', $output, $result);

    if (0 !== $result) {
      throw new \UnexpectedValueException(sprintf('Command failed: "%s" "%s"', $command, implode(PHP_EOL, $output)));
    }

    return $output;
  }

  /**
   * Clean build directory.
   */
  protected function cleanBuildDir() {
    $this->execute('rm -rf ' . \escapeshellarg($this->buildDirectory));
    $this->execute('mkdir ' . \escapeshellarg($this->buildDirectory));
  }

  /**
   * Clone a repository
   */
  protected function cloneRepository() {
    $target_path = $this->getTargetPath();
      $this->execute(
        'git clone '
        . \escapeshellarg($this->repository->getUrl())
        . ' ' . \escapeshellarg($target_path)
      );

      $this->execute(\sprintf(
        'cp -rf %s %s',
        escapeshellarg($target_path),
        escapeshellarg($target_path . '-original')
      ));
    }

  /**
   * @return string
   */
  protected function getTargetPath() {
    return $this->buildDirectory . '/' . $this->repository->getDirectoryName();
  }

  /**
   * @param $source_path
   * @param $target_path
   */
  protected function copyGenerated($source_path, $target_path) {
    $this->execute(
      sprintf(
        'cp -rf %s %s',
        \escapeshellarg($source_path),
        \escapeshellarg($target_path)
      )
    );
  }

  /**
   * @param callable $function
   * @param $path
   *
   * @return mixed
   */
  protected function runInPath(callable $function, $path) {
    $origin_path = getcwd();
    chdir($path);
    try {
      $return = $function();
    } finally {
      chdir($origin_path);
    }
    return $return;
  }

}
