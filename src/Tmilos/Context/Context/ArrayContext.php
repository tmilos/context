<?php
declare(strict_types=1);

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

    private function setParent(Context $parent = null)
    {
        $this->parent = $parent;
    }

    public function get(string $name, $default = null)
    {
        return array_key_exists($name, $this->items) ? $this->items[$name] : $default;
    }

    public function getOrCreate(string $name, string $class)
    {
        $result = $this->get($name);
        if (!$result) {
            if (!class_exists($class)) {
                throw new \InvalidArgumentException(sprintf('Class "%s" does not exist', $class));
            }
            $result = new $class;
            $this->items[$name] = $result;
            if ($result instanceof ArrayContext) {
                $result->setParent($this);
            }
        }

        return $this->items[$name];
    }

    public function getOrCall(string $name, callable $callable)
    {
        $result = $this->get($name);
        if (!$result) {
            $result = $callable($name, $this);
            $this->items[$name] = $result;
            if ($result instanceof ArrayContext) {
                $result->setParent($this);
            }
        }

        return $this->items[$name];
    }

    public function has(string $name) : bool
    {
        return array_key_exists($name, $this->items);
    }

    public function toArray() : array
    {
        return $this->_dump(false);
    }

    public function dump() : array
    {
        return $this->_dump(true);
    }

    public function set(string $name, $value)
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

    public function remove(string $name)
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

    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function count() : int
    {
        return count($this->items);
    }

    private function _dump(bool $includeClass) : array
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
