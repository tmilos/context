<?php

/*
 * This file is part of the Tmilos/Context package.
 *
 * (c) Milos Tomic <tmilos@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tmilos\Context;

interface Context extends \IteratorAggregate, \Countable, Dumpable
{
    /**
     * @return Context|null
     */
    public function getParent();

    /**
     * @return Context|null
     */
    public function getTopParent();

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed|Context
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     * @param string $class
     *
     * @return Context|mixed
     */
    public function getOrCreate($name, $class);

    /**
     * @param string   $name
     * @param callable $callable
     *
     * @return Context|mixed
     */
    public function getOrCall($name, $callable);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * @param string        $name
     * @param Context|mixed $value
     *
     * @return void
     */
    public function set($name, $value);

    /**
     * @param string $name
     *
     * @return Context|mixed|null
     */
    public function remove($name);

    /**
     * @return void
     */
    public function clear();

    /**
     * @return array
     */
    public function toArray();
}
