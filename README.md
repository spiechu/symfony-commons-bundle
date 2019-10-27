# Symfony Commons Bundle

| TravisCI | StyleCI | Scrutinizer | Code Coverage | Read the Docs |
|:--------:|:-------:|:-----------:|:-------------:|:-------------:|
| [![Build Status](https://travis-ci.org/spiechu/symfony-commons-bundle.svg?branch=master)](https://travis-ci.org/spiechu/symfony-commons-bundle) | [![StyleCI](https://styleci.io/repos/99513444/shield?style=flat)](https://styleci.io/repos/99513444) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/spiechu/symfony-commons-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/spiechu/symfony-commons-bundle/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/spiechu/symfony-commons-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/spiechu/symfony-commons-bundle/?branch=master) | [![Documentation Status](https://readthedocs.org/projects/symfony-commons-bundle/badge/?version=latest)](https://symfony-commons-bundle.readthedocs.io/en/latest/) |

## Intro

Main purpose of this bundle is to introduce some "missing" functionalities to Symfony Framework.

Consider this bundle as part of my preparations for Symfony Certification.
I can only learn by doing instead of passive DOC reading.

Bundle characteristics:
- plays well with [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
- [Symfony Flex ready](https://github.com/symfony/recipes-contrib/tree/master/spiechu/symfony-commons-bundle)

## Bundle rules

1. Every feature is disabled by default. You only enable what you need.
2. No event listeners hanging around when unneeded.
3. Provide sane defaults and extensive customisation possibilities.

## Features

### GET method override

Enabling this feature will allow you to use URLs like `http://myapp.com/mypath?_method=DELETE` or `PUT` to override HTTP GET request method.

Sometimes you might need such functionality for example in admin area to ban / delete users.
You can expose simple links and still have clean PUT / DELETE controller actions.

Full [documentation here](src/Resources/doc/get_method_override.md).

### Response schema validation

Response schema validation allows you to validate endpoint responses on-the-fly.
You just need to annotate controller action with `@ResponseSchemaValidator` annotation.

Typical use case is:

```php
// src/AppBundle/Controller/AdminController.php

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ResponseSchemaValidator;

class AdminController extends Controller
{
    /**
     * @Route("/", name="my_route")
     *
     * @ResponseSchemaValidator(
     *  json={
     *   200="@AppBundle/Resources/response_schema/my_route_200.json",
     *   500="@AppBundle/Resources/response_schema/my_route_500.json"
     *  }
     * )
     */
    public function indexAction(): Response
    {
        // ...
    }
}
```

Full [documentation here](src/Resources/doc/response_schema_validation.md).

### API versioning

When you have multiple API versions it's usually done by extending Controllers.
This bundle introduces handy `@ApiVersion` annotation.
You need to annotate your controller classes with this custom annotation and set version like:

```php
// src/AppBundle/Controller/V1_0/UserController.php

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;

/**
 * @ApiVersion("1.0")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="my_route")
     */
    public function indexAction(): Response
    {
        // ...
    }
}
```

Then in extending class:

```php
// src/AppBundle/Controller/V1_1/UserController.php

use Spiechu\SymfonyCommonsBundle\Annotation\Controller\ApiVersion;
use Spiechu\SymfonyCommonsBundle\Controller\V1_0\UserController as BaseUserController;

/**
 * @ApiVersion("1.1")
 */
class UserController extends BaseUserController
{
    /**
     * @Route("/", name="my_route")
     */
    public function indexAction(): Response
    {
        // ...
    }
}
```

From now on you can inject `Spiechu\SymfonyCommonsBundle\Service\ApiVersionProvider` service to your services and check what is the current request API version.

Full [documentation here](src/Resources/doc/api_versioning.md).

## Installation

I'm assuming you have Composer installed globally.

### Flex based installation (Symfony 3.4 and 4)

#### Download & enable the Bundle

Run console command in Symfony project directory:

```bash
composer req spiechu/symfony-commons-bundle
```

#### Enable some/all Bundle features

```yaml
# config/packages/spiechu_symfony_commons.yml

spiechu_symfony_commons:
    get_method_override:
        enabled: true
    response_schema_validation:
        enabled: true
    api_versioning:
        enabled: true
```

### Composer based installation (Symfony 3.4)

#### Download the Bundle

Run console command in Symfony project directory:

```bash
composer require spiechu/symfony-commons-bundle
```

#### Enable the Bundle

Enable the bundle by adding the following line in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Spiechu\SymfonyCommonsBundle\SpiechuSymfonyCommonsBundle(),
        ];

        // ...
    }
}
```

#### Enable some/all Bundle features

Preferred way of configuring Bundle is via YAML config:

```yaml
# app/config/config.yml

spiechu_symfony_commons:
    get_method_override:
        enabled: true
    response_schema_validation:
        enabled: true
    api_versioning:
        enabled: true
```

## Configuration

Configuration reference [can be found here](src/Resources/doc/configuration_reference.md).
