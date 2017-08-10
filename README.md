# Symfony Commons Bundle

| Travis |
|:------:|
| [![Build Status](https://travis-ci.org/spiechu/symfony-commons-bundle.svg?branch=master)](https://travis-ci.org/spiechu/symfony-commons-bundle) |

## Intro

Main purpose of this bundle is to introduce some "missing" functionalities to Symfony Framework.

Consider this bundle as part of my preparations for Symfony Certification.
I can only learn by doing instead of passive DOC reading.

Bundle plays well with [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle).

Bundle has high version requirements (Symfony 3.3, PHP 7.1).
I wanted to play with new stuff instead of more day-to-day work with legacy sh!t.

## Bundle rules

1. Every feature is disabled by default.
2. You only enable what you need.
3. No event listeners hanging around when unneeded.
4. Provide sane defaults and extensive customisation possibilities.
5. Every file runs on `declare(strict_types=1);`.

## Installation

### Download the Bundle

Assuming you have Composer installed globally, you just need to run console command in project directory:
 
```bash
composer require spiechu/symfony-commons-bundle
```

### Enable the Bundle

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

### Enable Bundle features

Preferred way of configuring Bundle is via YAML config:

```yml
# app/config/config.yml

spiechu_symfony_commons:
    get_method_override:
        enabled: true
    response_schema_validation:
        enabled: true
```

## Features

### GET method override

Symfony allows you to change HTTP method in forms via hidden `_method` form field ([described here](https://symfony.com/doc/current/form/action_method.html)).
This kind of override is not possible when using GET method.

With this bundle you can use `http://myapp.com/mypath?_method=DELETE` or `PUT`.

Beware however to not expose this override outside secured area, because it's still GET.
Web crawling robots will surely hit it and delete something ;-)

By definition GET requests should not modify state of the system, so consider this feature as a hack for admin area.
This way you can have clean `GET` / `POST` / `PUT` / `DELETE` endpoint actions in controller.

### Response schema validation

When exposing API to external services, sticking to agreed schema is crucial.
Decent API consumers will most probably validate incoming data.
How will your API look like when you won't give data in proper form as promised?

Response schema validation allows you to validate endpoint responses on-the-fly.
You just need to annotate controller action with `@ResponseSchemaValidator`. Typical use case is:

```php
// src/AppBundle/Controller/MyController.php

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
