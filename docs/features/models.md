---
title: Models
---

Ampersand does not include features related to models but it works well with [Hero](http://alterfw.github.io/hero).

To install Hero, just run the bash below in your theme's root:

    composer require alterfw/hero

Hero is Alter's main dependency and take cares of all model-binding to WordPress Post Types and taxonomies.

### How to use Hero in Routes

```php
Route::get('/', function() {
  $this->render('cars.index', ['cars' => Car::all()]);
});
```

Read more about how to use Hero in the [documentation](http://alterfw.github.io/hero).
