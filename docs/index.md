---
title: Installation
---

Ampersand requires PHP 5.4.0 or newer.

Installation via [Composer](https://getcomposer.org/)

    composer require alterfw/ampersand

Insert into your `functions.php`:

```php
require 'vendor/autoload.php';
```

## Usage

```php
Route::get('/hello/:name', function($name){
  echo 'Hello, $name';
});
```


## License

The MIT License (MIT). Please see [License File](https://github.com/alterfw/ampersand/blob/master/LICENSE) for more information.
