Github Publisher
==========================

Install
-------
`composer require vijaycs85/github-publisher`

Usage
-----

```$php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Vijaycs85\GithubPublisher\Repository;
use Vijaycs85\GithubPublisher\Builder;

// Github repository name in username/projectname format.
$repository = new Repository('vijaycs85/static-site', \getenv('GITHUB_TOKEN'));

// Pass location of clone.
$builder = new Builder(__DIR__ . '/build', $repository);

// Directory that contains souce code and branch to push.
$builder->publish(__DIR__ . '/public', 'gh-pages');
```

>NOTE: Get the token from [Personal access tokens](https://github.com/settings/tokens).
