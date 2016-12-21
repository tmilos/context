<?php

namespace Tests\Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Action\AbstractWrappedAction;
use Tmilos\Context\Context;

class AbstractWrappedActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_calls_inner_execute()
    {
        $context = $this->getContextMock();
        $innerAction = $this->getActionMock();
        $action = $this->getAbstractWrappedActionMock($innerAction);

        $innerAction->expects($this->once())
            ->method('execute')
            ->with($context);

        $action->execute($context);
    }

    public function test_before_action_is_called()
    {
        $context = $this->getContextMock();
        $innerAction = $this->getActionMock();
        $action = $this->getAbstractWrappedActionMock($innerAction);

        $action->expects($this->once())
            ->method('beforeAction')
            ->with($context);

        $action->execute($context);
    }

    public function test_after_action_is_called()
    {
        $context = $this->getContextMock();
        $innerAction = $this->getActionMock();
        $action = $this->getAbstractWrappedActionMock($innerAction);

        $action->expects($this->once())
            ->method('afterAction')
            ->with($context);

        $action->execute($context);
    }

    public function test_order_of_calls()
    {
        $context = $this->getContextMock();
        $innerAction = $this->getActionMock();
        $action = $this->getAbstractWrappedActionMock($innerAction);

        $calls = [];

        $action->expects($this->once())
            ->method('beforeAction')
            ->willReturnCallback(function () use (&$calls) {
                $calls[] = 'beforeAction';
            });
        $innerAction->expects($this->once())
            ->method('execute')
            ->willReturnCallback(function () use (&$calls) {
                $calls[] = 'execute';
            });
        $action->expects($this->once())
            ->method('afterAction')
            ->willReturnCallback(function () use (&$calls) {
                $calls[] = 'afterAction';
            });

        $action->execute($context);

        $this->assertEquals(['beforeAction', 'execute', 'afterAction'], $calls);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractWrappedAction
     */
    private function getAbstractWrappedActionMock(Action $innerAction)
    {
        return $this
            ->getMockBuilder(AbstractWrappedAction::class)
            ->setConstructorArgs([$innerAction])
            ->setMethods(['beforeAction', 'afterAction'])
            ->getMock()
        ;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Context
     */
    private function getContextMock()
    {
        return $this->createMock(Context::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Action
     */
    private function getActionMock()
    {
        return $this->createMock(Action::class);
    }
}
