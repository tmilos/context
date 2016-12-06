<?php

namespace Tests\Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Action\ArrayCompositeAction;

class ArrayCompositeActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        $composite = new ArrayCompositeAction();
        $this->assertCount(0, $composite->getActions());
    }

    public function test_can_be_constructed_with_array_of_actions()
    {
        $composite = new ArrayCompositeAction([$this->getActionMock(), $this->getActionMock()]);
        $this->assertCount(2, $composite->getActions());
    }

    public function test_can_add_child_action()
    {
        $composite = new ArrayCompositeAction();
        $composite->add($this->getActionMock());
        $this->assertCount(1, $composite->getActions());
    }

    public function test_execute_called_on_each_child()
    {
        $context = $this->getContextMock();

        $action1 = $this->getActionMock();
        $action1->expects($this->once())
            ->method('execute')
            ->with($context);

        $composite = new ArrayCompositeAction([$action1]);

        $action2 = $this->getActionMock();
        $action2->expects($this->once())
            ->method('execute')
            ->with($context);
        $composite->add($action2);

        $composite->execute($context);
    }

    public function test_map()
    {
        $action1 = $this->getActionMock();
        $action2 = $this->getActionMock();
        $action1mapped = $action2mapped = false;
        $action2replacement = null;

        $composite = new ArrayCompositeAction([$action1, $action2]);

        $composite->map(function (Action $action) use ($action1, $action2, &$action1mapped, &$action2mapped, &$action2replacement) {
            if ($action === $action1) {
                $action1mapped = true;

                return $action;
            } elseif ($action === $action2) {
                $action2mapped = true;
                $action2replacement = $this->getActionMock();

                return $action2replacement;
            } else {
                throw new \RuntimeException('Unexpected action given in map() method');
            }
        });

        $this->assertTrue($action1mapped);
        $this->assertTrue($action2mapped);

        $children = $composite->getActions();
        $this->assertCount(2, $children);
        $this->assertSame($action1, $children[0]);
        $this->assertSame($action2replacement, $children[1]);
    }

    public function test_debug_tree()
    {
        $innerComposite = new ArrayCompositeAction([new FooAction(), new BarAction()]);
        $this->assertCount(2, $innerComposite->getActions());
        $outerComposite = new ArrayCompositeAction([new FooAction(), $innerComposite, new BarAction()]);
        $this->assertCount(3, $outerComposite->getActions());

        $actualTree = $outerComposite->dump();

        $expectedTree = [
            ArrayCompositeAction::class => [
                FooAction::class => [],
                ArrayCompositeAction::class => [
                    FooAction::class => [],
                    BarAction::class => [],
                ],
                BarAction::class => [],
            ],
        ];

        $this->assertEquals($expectedTree, $actualTree);
    }

    public function test_to_string_returns_json_debug_tree_string()
    {
        $innerComposite = new ArrayCompositeAction([new FooAction(), new BarAction()]);
        $this->assertCount(2, $innerComposite->getActions());
        $outerComposite = new ArrayCompositeAction([new FooAction(), $innerComposite, new BarAction()]);
        $this->assertCount(3, $outerComposite->getActions());

        $actualValue = json_encode($outerComposite->dump(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $expectedValue = <<<EOT
{
    "Tmilos\\\\Context\\\\Action\\\\ArrayCompositeAction": {
        "Tests\\\\Tmilos\\\\Context\\\\Action\\\\FooAction": [],
        "Tmilos\\\\Context\\\\Action\\\\ArrayCompositeAction": {
            "Tests\\\\Tmilos\\\\Context\\\\Action\\\\FooAction": [],
            "Tests\\\\Tmilos\\\\Context\\\\Action\\\\BarAction": []
        },
        "Tests\\\\Tmilos\\\\Context\\\\Action\\\\BarAction": []
    }
}
EOT;

        $this->assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Tmilos\Context\Action
     */
    private function getActionMock()
    {
        return $this->createMock('Tmilos\Context\Action');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Tmilos\Context\Context
     */
    private function getContextMock()
    {
        return $this->createMock('Tmilos\Context\Context');
    }
}
