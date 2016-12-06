<?php

namespace Tests\Tmilos\Context\Context;

use Tmilos\Context\Context;
use Tmilos\Context\Context\ArrayContext;

class ArrayContextTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new ArrayContext();
    }

    public function test_get_returns_default_value_when_nothing_already_set()
    {
        $context = new ArrayContext();
        $value = $context->get($name = 'name', $defaultValue = 'default');
        $this->assertEquals($defaultValue, $value);
    }

    public function test_get_returns_set_value()
    {
        $context = new ArrayContext();
        $expectedValue = new \stdClass();
        $name = 'foo';
        $context->set($name, $expectedValue);
        $value = $context->get($name);
        $this->assertSame($expectedValue, $value);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class "Class\That\Does\Not\Exist" does not exist
     */
    public function test_get_or_create_throws_exception_if_class_does_not_exist()
    {
        $context = new ArrayContext();
        $context->getOrCreate('name', 'Class\That\Does\Not\Exist');
    }

    public function test_get_or_create_creates_new_instance_child_of_specified_class()
    {
        $context = new ArrayContext();
        $child = $context->getOrCreate('foo', ArrayContext::class);
        $this->assertInstanceOf(ArrayContext::class, $child);
    }

    public function test_get_or_create_created_child_instance_has_parent()
    {
        $context = new ArrayContext();
        $child = $context->getOrCreate('foo', ArrayContext::class);
        $this->assertSame($context, $child->getParent());
    }

    public function test_get_or_call_calls_callable_with_name_and_context_arguments()
    {
        $context = new ArrayContext();
        $expectedName = 'foo';
        $context->getOrCall($expectedName, function ($name, $parent) use ($context, $expectedName) {
            $this->assertSame($context, $parent);
            $this->assertEquals($expectedName, $name);
        });

    }

    public function test_get_or_call_returns_value_callable_returned()
    {
        $context = new ArrayContext();
        $expectedValue = new ArrayContext();
        $value = $context->getOrCall('foo', function () use ($expectedValue) {
            return $expectedValue;
        });
        $this->assertSame($expectedValue, $value);
    }

    public function test_has_returns_false_if_name_not_set()
    {
        $context = new ArrayContext();
        $this->assertFalse($context->has('foo'));
    }

    public function test_has_returns_true_for_the_set_name()
    {
        $context = new ArrayContext();
        $context->set('foo', 123);
        $this->assertTrue($context->has('foo'));
    }

    public function test_set_sets_parent_if_argument_is_context()
    {
        $context = new ArrayContext();
        $child = new ArrayContext();
        $context->set('foo', $child);
        $this->assertSame($context, $child->getParent());
    }

    public function test_set_unsets_parent_when_replacing_old_context_value()
    {
        $name = 'foo';
        $context = new ArrayContext();
        $old = $context->getOrCreate($name, ArrayContext::class);
        $new = new ArrayContext();
        $context->set($name, $new);
        $this->assertNull($old->getParent());
    }

    public function test_set_does_nothing_if_same_value_already_set_with_same_name()
    {
        $context = new ArrayContext();
        $value = new \stdClass();
        $name = 'foo';
        $context->set($name, $value);
        $this->assertCount(1, $context);
        $context->set($name, $value);
        $this->assertCount(1, $context);
    }

    public function test_removes_specified_name()
    {
        $context = new ArrayContext();
        $context->set('foo', 123);
        $context->remove('foo');
        $this->assertFalse($context->has('foo'));
    }

    public function test_remove_unsets_parent()
    {
        $context = new ArrayContext();
        $child = $context->getOrCreate('foo', ArrayContext::class);
        $context->remove('foo');
        $this->assertNull($child->getParent());
    }

    public function test_get_top_parent_returns_this_when_no_parent()
    {
        $context = new ArrayContext();
        $this->assertSame($context, $context->getTopParent());
    }

    public function test_get_top_parent_returns_top_most_parent()
    {
        $root = new ArrayContext();
        $c1 = $root->getOrCreate('foo', ArrayContext::class);
        $c2 = $c1->getOrCreate('bar', ArrayContext::class);
        $this->assertSame($root, $c2->getTopParent());
    }

    public function test_clear_removes_all_items()
    {
        $context = new ArrayContext();
        $context->set('a', 1);
        $context->set('b', 2);
        $context->clear();
        $this->assertCount(0, $context);
    }

    public function test_clear_unsets_context_values_parent()
    {
        $context = new ArrayContext();
        $child = $context->getOrCreate('foo', ArrayContext::class);
        $context->clear();
        $this->assertNull($child->getParent());
    }

    public function test_can_be_iterated()
    {
        $context = new ArrayContext();
        $context->set('a', 1);
        $context->set('b', 'x');
        $values = [];
        foreach ($context as $k=>$v) {
            $values[$k] = $v;
        }
        $this->assertEquals(
            ['a' => 1, 'b' => 'x'],
            $values
        );
    }

    public function test_count()
    {
        $context = new ArrayContext();
        $this->assertCount(0, $context);
        $context->set('a', 1);
        $this->assertCount(1, $context);
        $context->set('a', 1);
        $this->assertCount(1, $context);
        $context->set('b', 2);
        $this->assertCount(2, $context);
    }

    public function test_dump()
    {
        $context = $this->getContextTree();
        $dump = $context->dump();

        $expected = [
            '__class__' => ArrayContext::class,
            'a' => 1,
            'c1' => [
                '__class__' => ArrayContext::class,
                'b' => 'x',
            ],
            'c2' => [
                '__class__' => ArrayContext::class,
                'm' => 'hmmm',
                'arr' => [1, 2, 3],
                'c3' => [
                    '__class__' => ArrayContext::class,
                ],
            ],
        ];

        $this->assertEquals($dump, $expected);
    }

    public function test_to_array()
    {
        $context = $this->getContextTree();
        $arr = $context->toArray();

        $expected = [
            'a' => 1,
            'c1' => [
                'b' => 'x',
            ],
            'c2' => [
                'm' => 'hmmm',
                'arr' => [1, 2, 3],
                'c3' => [],
            ],
        ];

        $this->assertEquals($arr, $expected);
    }

    /**
     * @return Context
     */
    private function getContextTree()
    {
        $context = new ArrayContext();
        $context->set('a', 1);
        $c1 = $context->getOrCreate('c1', ArrayContext::class);
        $c1->set('b', 'x');
        $c2 = $context->getOrCreate('c2', ArrayContext::class);
        $c2->set('m', 'hmmm');
        $c2->set('arr', [1, 2, 3]);
        $c2->getOrCreate('c3', ArrayContext::class);

        return $context;
    }
}
