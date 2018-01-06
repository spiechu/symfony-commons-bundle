Symfony allows you to change HTTP method in forms via hidden `_method` form field [described here](https://symfony.com/doc/current/form/action_method.html).
This kind of override is not possible when using GET method.

With this bundle you can use `http://myapp.com/mypath?_method=DELETE` or `PUT`.

Beware however to not expose this override outside secured area, because it's still GET.
Web crawling robots will surely hit it and delete something ;-)

By definition GET requests should not modify state of the system, so consider this feature as a hack for admin area.
This way you can have clean `GET` / `POST` / `PUT` / `DELETE` endpoint actions in controller.

By default feature is disabled. You need to set `enabled: true` flag to make it work.

By default only DELETE, POST and PUT methods are allowed to override.
You can modify this setting using `allow_methods_override` string array.

If for some reasons `_method` query param needs to be changed then you can use `query_param_name` setting.

You may completely replace event listener which modifies request HTTP method by your own service using `listener_service_id`.
First constructor argument will receive query param name (`_method` by default) and second will receive array of methods allowed to override.

```php
/**
 * @param string   $queryParamName
 * @param string[] $methodsToOverride
 */
public function __construct(string $queryParamName, array $methodsToOverride)
```

Also you need to create typical method `onKernelRequest` accepting `Symfony\Component\HttpKernel\Event\GetResponseEvent` object as parameter.

```php
/**
 * @param GetResponseEvent $getResponseEvent
 */
public function onKernelRequest(GetResponseEvent $getResponseEvent): void
```
