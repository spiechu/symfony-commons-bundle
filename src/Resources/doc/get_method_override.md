Symfony allows you to change HTTP method in forms via hidden `_method` form field ([described here](https://symfony.com/doc/current/form/action_method.html)).
This kind of override is not possible when using GET method.

With this bundle you can use `http://myapp.com/mypath?_method=DELETE` or `PUT`.

Beware however to not expose this override outside secured area, because it's still GET.
Web crawling robots will surely hit it and delete something ;-)

By definition GET requests should not modify state of the system, so consider this feature as a hack for admin area.
This way you can have clean `GET` / `POST` / `PUT` / `DELETE` endpoint actions in controller.
