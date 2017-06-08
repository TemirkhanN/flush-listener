# temirkhan/flush-listener
Automatically flushes entity manager on valid symfony response status code.

If for some reason you want prevent flushing
use

```PHP
#http://symfony.com/doc/current/components/event_dispatcher.html

$dispatcher->dispatch('transaction.rollback');
```
## Installation

Install bundle by composer

>  composer require temirkhan/flush-listener

Enable it in your app/AppKernel.php

```PHP
<?php
#app/AppKernel.php
...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...,
            new Temirkhan\FlushListenerBundle\TemirkhanFlushListenerBundle(),
        ];
    }
    
...
```

This is it. Now when symfony finishes handling request and return response
entity manager will be flushed based on response status code.

This mechanism suites postgresql.