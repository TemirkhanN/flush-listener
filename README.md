# temirkhan/flush-listener
Automatically flushes entity manager on valid symfony response status code.

[![Build Status](https://travis-ci.org/TemirkhanN/onresponse-flush-listener.svg?branch=master)](https://travis-ci.org/TemirkhanN/onresponse-flush-listener)
[![Coverage Status](https://coveralls.io/repos/github/TemirkhanN/onresponse-flush-listener/badge.svg)](https://coveralls.io/github/TemirkhanN/onresponse-flush-listener)

Works with symfony event dispatcher
> [component](http://symfony.com/doc/current/components/event_dispatcher.html)

To force flushing

```PHP

$dispatcher->dispatch('transaction.commit');
```

To prevent flushing

```PHP

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