Choclo
======

![by  Artful Magpie](https://farm9.staticflickr.com/8056/8135145941_7a9ca3940e_m_d.jpg)
(by  [Artful Magpie](https://www.flickr.com/photos/kmtucker/8135145941))

Choclo is a two-step configuration framework.

Usage
-----

```php
use Guide42\Choclo\Configurator;

class MyApp extends Configurator
{
    const PHASE_ROUTES = 60;

    public function addRoute($name, $pattern, \Closure $controller)
    {
        $connect = function() use ($name, $pattern, $controller) {
            $route = new Route($pattern, array(
                '_controller' => $controller,
            ));

            $routes = $this->getRegistry()->get('Symfony\Component\Routing\RouteCollection');
            $routes->add($name, $route);
        };
        $this->register('route-' . $name, $connect, self::PHASE_ROUTES);
    }
}
```

Badges
------

[![Latest Stable Version](https://poser.pugx.org/guide42/choclo/v/stable.svg)](https://packagist.org/packages/guide42/choclo)
[![Build Status](https://travis-ci.org/guide42/choclo.svg?branch=master)](https://travis-ci.org/guide42/choclo)
[![Coverage Status](https://img.shields.io/coveralls/guide42/choclo.svg)](https://coveralls.io/r/guide42/choclo)
