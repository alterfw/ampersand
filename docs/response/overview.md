---
title: Response
---

The HTTP response returned to the client will have a body. The HTTP body is the actual content of the HTTP response
delivered to the client. You can use the Response object to set the HTTP response’s body. The response object is binded to route and middlewares closures, so you can access usind `$this`:

```php
Route::get('/', function(){

  // Overwrite response body
  $this->setBody('Foo');

  // Append response body
  $this->write('Bar');

});
```

When you overwrite or append the response object’s body, the response object will automatically set the
**Content-Length** header based on the bytesize of the new response body.

You can fetch the response object’s body like this:

```php
$body = $this->getBody();
```


## Status

The HTTP response returned to the client will have a status code indicating the response’s type
(e.g. 200 OK, 400 Bad Request, or 500 Server Error). You can use the Response object to set the
HTTP response’s status like this:

```php
$this->setStatus(400);
```

You only need to set the response object’s status if you intend to return an HTTP response that *does not* have
a 200 OK status. You can just as easily fetch the response object’s current HTTP status by invoking the same
method without an argument, like this:

```php
$status = $this->getStatus();
```


## Headers

The HTTP response returned to the HTTP client will have a header. The HTTP header is a list of keys and values that
provide metadata about the HTTP response. You can use the Response object to set the HTTP
response’s header.

```php
$this->headers->set('Content-Type', 'application/json');
```

You may also fetch headers from the response object's `headers` property, too:

```php
$this->headers->get('Content-Type');
```


If a header with the given name does not exist, `null` is returned. You may specify header names with upper, lower,
or mixed case with dashes or underscores. Use the naming convention with which you are most comfortable.
