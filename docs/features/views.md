---
title: Views and Layouts
---

Ampersand uses all the power of [Twig](http://twig.sensiolabs.org/) to render templates.

You can render a view using the `render()` method of `Callback` object.

```php
Route::get('/', function(){
  $this->render('index'); // Will render views/index.html
});
```

You can also organize your views into folders:

```php
$this->render('cars.index'); // Will render views/cars/index.html
```

And pass data to the views:

```php
$this->render('cars.index', ['car' => $car]); // Will render views/cars/index.html passing $car
```

## Using Twig templates

You can read about how to use Tiwg templates in his [documentation](http://twig.sensiolabs.org/documentation).

### Layouts

To use layouts with Ampersand you can use the [`extends` method](http://twig.sensiolabs.org/doc/tags/extends.html) of Twig.
