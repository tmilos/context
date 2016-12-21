<?php

/*
 * This file is part of the Tmilos/Context package.
 *
 * (c) Milos Tomic <tmilos@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tmilos\Context\Context;

use Tmilos\Context\Context;

class ArrayContext implements Context
{
    /** @var array */
    private $items = [];

    /** @var Context */
    private $parent;

    public function getParent()
    {
        return $this->parent;
    }

    public function getTopParent()
    {
        if ($this->parent) {
            return $this->parent->getTopParent();
        }

        return $this;
    }

    protected function setParent(Context $parent = null)
    {
        $this->parent = $parent;
    }

    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->items) ? $this->items[$name] : $default;
    }

    public function getOrCreate($name, $class)
    {
        $result = $this->get($name);
        if (!$result) {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" does not exist', $class));
            }
            $result = new $class();
            $this->items[$name] = $result;
            if ($result instanceof self) {
                $result->setParent($this);
            }
        }

        return $this->items[$name];
    }

    public function getOrCall($name, $callable)
    {
        $result = $this->get($name);
        if (!$result) {
            $result = $callable($name, $this);
            $this->items[$name] = $result;
            if ($result instanceof self) {
                $result->setParent($this);
            }
        }

        return $this->items[$name];
    }

    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    public function toArray()
    {
        return $this->doDump(false);
    }

    public function dump()
    {
        return $this->doDump(true);
    }

    public function set($name, $value)
    {
        $existing = isset($this->items[$name]) ? $this->items[$name] : null;
        if ($existing === $value) {
            return;
        }
        $this->items[$name] = $value;
        if ($value instanceof Context) {
            $value->setParent($this);
        }
        if ($existing instanceof Context) {
            $existing->setParent(null);
        }
    }

    public function remove($name)
    {
        $result = null;
        if (array_key_exists($name, $this->items)) {
            $result = $this->items[$name];
            if ($result instanceof Context) {
                $result->setParent(null);
            }
            unset($this->items[$name]);
        }

        return $result;
    }

    public function clear()
    {
        foreach ($this->items as $item) {
            if ($item instanceof Context) {
                $item->setParent(null);
            }
        }

        $this->items = [];
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    private function doDump($includeClass)
    {
        $result = [];
        if ($includeClass) {
            $result['__class__'] = get_class($this);
        }

        foreach ($this->items as $name => $item) {
            if ($item instanceof Context) {
                $result[$name] = $includeClass ? $item->dump() : $item->toArray();
            } else {
                $result[$name] = $item;
            }
        }

        return $result;
    }
}
