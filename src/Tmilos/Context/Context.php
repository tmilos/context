<?php
declare(strict_types=1);

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
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @param string $class
     *
     * @return Context|mixed
     */
    public function getOrCreate(string $name, string $class);

    /**
     * @param string $name
     * @param callable $callable
     *
     * @return Context|mixed
     */
    public function getOrCall(string $name, callable $callable);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * @param string        $name
     * @param Context|mixed $value
     *
     * @return void
     */
    public function set(string $name, $value);

    /**
     * @param string $name
     *
     * @return Context|mixed|null
     */
    public function remove(string $name);

    /**
     * @return void
     */
    public function clear();

    /**
     * @return array
     */
    public function toArray() : array;
}
