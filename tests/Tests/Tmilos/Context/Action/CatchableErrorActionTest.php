<?php

namespace Tests\Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Action\CatchableErrorAction;
use Tmilos\Context\Context\ArrayContext;
use Tmilos\Context\Context\ExceptionContext;

class CatchableErrorActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructs_with_two_actions()
    {
        new CatchableErrorAction($this->getActionMock(), $this->getActionMock());
    }

    public function test_execute_calls_first_action()
    {
        $mainAction =  new CatchableErrorAction(
            $firstAction = $this->getActionMock(),
            $secondAction = $this->getActionMock()
        );
        $context = new ArrayContext();
        $firstAction->expects($this->once())
            ->method('execute')
            ->with($context);
        $secondAction->expects($this->never())
            ->method('execute');

        $mainAction->execute($context);
    }

    public function test_execute_calls_second_action_if_first_throws_exception_and_add_exception_to_context()
    {
        $mainAction =  new CatchableErrorAction(
            $firstAction = $this->getActionMock(),
            $secondAction = $this->getActionMock()
        );
        $context = new ArrayContext();
        $firstAction->expects($this->once())
            ->method('execute')
            ->with($context)
            ->willThrowException($exception = new \Exception());
        $secondAction->expects($this->once())
            ->method('execute')
            ->with($context)
        ;

        $mainAction->execute($context);

        /** @var ExceptionContext $exceptionContext */
        $exceptionContext = $context->get('exception');
        $this->assertNotNull($exceptionContext);
        $this->assertInstanceOf(ExceptionContext::class, $exceptionContext);
        $this->assertSame($exception, $exceptionContext->getException());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Tmilos\Context\Action
     */
    private function getActionMock()
    {
        return $this->createMock(Action::class);
    }
}
