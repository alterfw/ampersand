---
title: Session
---

The Session class helps you handle session items and flash messages.

## Interacting with session

You can use the `set()`, `get()` and `has()` methods to interact with session items.

```php
Session::set('hello', 'world');
if(Session::has('hello'))
  echo Session::get('hello');
```

The above script will output this:

    hello
    
    
## Flash messages

You can use the `flash()` method to deal with flash messages. Flash messages will be available via the `get()` method only in the next request.

```php
Session::flash('You have one message!');
```

You can also specify the type of the message: 

```php
Session::flash('Unexpected error!', 'error');
```

The available options are: `message`, `error`, `info` and `warning`.

## Accessing Session in the views

An instance of `Session` is available globally in all twig templates rendered by Ampersand.

```php
{% if session->has('message') %}
{{ session->get('message') }}
{% endif %}
```
