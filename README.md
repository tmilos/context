# Context


[![Author](http://img.shields.io/badge/author-@tmilos-blue.svg?style=flat-square)](https://twitter.com/tmilos77)
[![Build Status](https://travis-ci.org/tmilos/context.svg?branch=master)](https://travis-ci.org/tmilos/context)
[![Coverage Status](https://coveralls.io/repos/github/tmilos/context/badge.svg?branch=master)](https://coveralls.io/github/tmilos/context?branch=master)
[![License](https://img.shields.io/packagist/l/tmilos/context.svg)](https://packagist.org/packages/tmilos/context)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4a1c02a5-9f73-4702-8c59-0805cb6d3f5e/small.png)](https://insight.sensiolabs.com/projects/4a1c02a5-9f73-4702-8c59-0805cb6d3f5e)

A hierarchical context tree PHP library, with hierarchical parameter bags - context, and actions working with them.


## ArrayContext

The ``ArrayContext`` is the default implementation of the ``Context`` interface. It acts as a hierarchical parameter bags, when
items can also be other contexts.

``` php
<?php

$context = new ArrayContext();

// can set/get/has values like to parameter bag
$context->set('foo', 123);
$context->get('foo'); // 123
$context->has('foo'); // true

// can iterate
foreach ($context as $key => $value) { }

// can create sub-contexts
$subContext = $context->getOrCreate('sub', SomeContext:class);

// can callback for creation of sub-context
$subContext = $context->getOrCall('sub', function () use ($dependencies) {
    return new SomeContext($dependencies);
});

// can get parent context
$subContext->getParent() === $context;

// can get root context
$leafContext = $subContext->getOrCreate('leaf', ArrayContext:class);
$leafContext->getTopParent() === $context;

// can dump to array
$context->set('bar', ['a' => 1, 'b' => 'x']);
$context->toArray(); // ['foo' => 123, 'bar' => ['a' => 1, 'b' => 'x'], 'sub' => ['leaf' => []]]
```


## ExceptionContext

The ``ExceptionContext`` holds single exception, and when other exception is added can chain with other ``ExceptionContext``.

``` php
$context = new ExceptionContext(new \Exception('first'));
$context->addException(new \Exception('second'));
$context->addException(new \Exception('third'));

$context->getException()->getMessage(); // first
$context->getLastException()->getMessage(); // third
```


## Action

The ``Action`` interface defines an action that executes on a ``Context``.


### Composite Action

The ``CompositeAction`` compose several actions into one, and when executed calls each child action in the order they were added.


### Action Mapper

The ``ActionMapper`` is an invokable interface that gets called to return new instances of actions that will replace the old ones.
Can be used for example to wrap actions to record their execution times and to log details.

``` php
<?php
$composite = new ArrayCompositeAction();
$composite->add($actionOne);
$composite->add($actionTwo);
$mapper = new ActionLogWrapper(); // some implementation of the ActionMapper interface
$composite->map($mapper); // inner actions gets replaced with return values of the mapper
```


### Catchable Error Action

The ``CatchableErrorAction`` is constructed with two actions. On execute first "main" action is called, and if it throws an
``Exception``, an ``ExceptionContext`` is added to the supplied context and second "error" action is called.


### Abstract Wrapped Action

The ``AbstractWrappedAction`` is an abstract implementation of the ``Action`` interface, that takes inner action as constructor
argument, and on execute first calls own ``beforeAction(Context $context)`` protected method, then the inner action ``execute()`` method,
and finally own ``afterAction(Context $context)``.

