---
title: Request
---

Use the request object's `getBody()` method to fetch the raw HTTP request body sent by the HTTP client. This is
particularly useful for applications that consume JSON or XML requests.

```php
Route::get('/hello/:name', function(){
  $body = $this->request->getBody();
});
```


## How to Deal with Content-Types

By default Ampersand does not parse any other content-type other than the standard form data because PHP does not support it. That means if you attempt to post application/json you will not be able to access the data via `$this->request->post()`;

To solve this you must parse the content type yourself. You can either do this on a per-route basis

```php
//For application/json
$data = json_decode($this->request->getBody());
```

## Cookies

### Get Cookies

Ampersand will automatically parse cookies sent with the current HTTP request. You can fetch cookie values
with the request object like this:

```php
$cookies = $this->request->cookies;
```

Only Cookies sent with the current HTTP request are accessible with this method. If you set a cookie during the
current request, it will not be accessible with this method until the subsequent request.

### Cookie Encryption

You can optionally choose to encrypt all cookies stored on the HTTP client with the Slim app's `cookies.encrypt`
setting. When this setting is `true`, all cookies will be encrypted using your designated secret key and cipher.

It's really that easy.

# Headers

Ampersand will automatically parse all HTTP request headers. You can access the request headers using the
request object's `headers` property.

```php
// Get request headers as associative array
$headers = $this->request->headers;

// Get the ACCEPT_CHARSET header
$charset = $this->request->headers->get('ACCEPT_CHARSET');
```

The HTTP specification states that HTTP header names may be uppercase, lowercase, or mixed-case. Slim is smart enough
to parse and return header values whether you request a header value using upper, lower, or mixed case header name,
with either underscores or dashes. So use the naming convention with which you are most comfortable.

# Ajax Requests

When using a Javascript framework like MooTools or jQuery to execute an XMLHttpRequest, the XMLHttpRequest will
usually be sent with a **X-Requested-With** HTTP header. The Slim application will detect the HTTP
request’s **X-Requested-With** header and flag the request as such. If for some reason an XMLHttpRequest cannot
be sent with the **X-Requested-With** HTTP header, you can force the Ampersand to assume an HTTP request
is an XMLHttpRequest by setting a GET, POST, or PUT parameter in the HTTP request named “isajax” with a truthy value.

Use the request object's `isAjax()` or `isXhr()` method to tell if the current request is an XHR/Ajax request:

```php
$isXHR = $app->request->isAjax();
$isXHR = $app->request->isXhr();
```
