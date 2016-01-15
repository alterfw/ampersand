---
title: Request Methods
---

Every HTTP request has a method (e.g. GET or POST). You can obtain the current HTTP request method via the Request object:

```php
/**
 * What is the request method?
 * @return string (e.g. GET, POST, PUT, DELETE)
 */
$req->getMethod();

/**
 * Is this a GET request?
 * @return bool
 */
$req->isGet();

/**
 * Is this a POST request?
 * @return bool
 */
$req->isPost();

/**
 * Is this a PUT request?
 * @return bool
 */
$req->isPut();

/**
 * Is this a DELETE request?
 * @return bool
 */
$req->isDelete();

/**
 * Is this a HEAD request?
 * @return bool
 * @return bool
 */
$req->isHead();

/**
 * Is this a OPTIONS request?
 * @return bool
 */
$req->isOptions();

/**
 * Is this a PATCH request?
 * @return bool
 */
$req->isPatch();

/**
 * Is this a XHR/AJAX request?
 * @return bool
 */
$req->isAjax();
```
