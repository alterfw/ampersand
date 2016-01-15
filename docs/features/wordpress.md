---
title: WordPress Templates
---

If you need to use some WordPress standard templates in you theme, you can do it with Ampersand. Just use the `template()` method from the Response object.

```php
Route::get('/search', function(){
  $this->template('search'); // Will require (not render) your_theme/search.php
});
```
