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
- has high version requirements (Symfony 3.3, PHP 7.1).

## Bundle rules

1. Every feature is disabled by default.
2. You only enable what you need.
3. No event listeners hanging around when unneeded.
4. Provide sane defaults and extensive customisation possibilities.
5. Every file runs on `declare(strict_types=1);`.

## Installation

I'm assuming you have Composer installed globally.

### Flex based installation (Symfony 3.4 and 4)

#### Download & enable the Bundle

Run console command in Symfony project directory:

```bash
composer req spiechu/symfony-commons-bundle
```

#### Enable Bundle features

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

### Composer based installation (Symfony 3.3)

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

#### Enable Bundle features

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

Configuration reference [can be found here](src/Resources/doc/configuration_reference.md).

## Features

### GET method override

Full [documentation here](src/Resources/doc/get_method_override.md).

Symfony allows you to change HTTP method in forms via hidden `_method` form field ([described here](https://symfony.com/doc/current/form/action_method.html)).
This kind of override is not possible when using GET method.

With this bundle you can use `http://myapp.com/mypath?_method=DELETE` or `PUT`.

Beware however to not expose this override outside secured area, because it's still GET.
Web crawling robots will surely hit it and delete something ;-)

By definition GET requests should not modify state of the system, so consider this feature as a hack for admin area.
This way you can have clean `GET` / `POST` / `PUT` / `DELETE` endpoint actions in controller.

### Response schema validation

Full [documentation here](src/Resources/doc/response_schema_validation.md).

When exposing API to external services, sticking to agreed schema is crucial.
Decent API consumers will most probably validate incoming data.
How will your API look like when you won't give data in proper form as promised?

Response schema validation allows you to validate endpoint responses on-the-fly.
You just need to annotate controller action with `@ResponseSchemaValidator`. Typical use case is:

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

### API versioning

Full [documentation here](src/Resources/doc/api_versioning.md).

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
