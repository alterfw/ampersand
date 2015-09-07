---
title: URL
---

The URL class helps you to get URLs for your routes.
An instance of URL is available globally for all twig views rendered by Ampersand:

```php
<a href="{{ url->to('/') }}">Home</a>
```

You can also create more complex URLs passing attributes:

```php
<a href="{{ url->to('/hello/:name/:age', 'Sergio, '23') }}">Hello!</a>
```

This code above will generate the following output:

    http://yousite.com/hello/Sergio/23