<?php

namespace Tests\Tmilos\Context\Context;

use Tmilos\Context\Context\ExceptionContext;

class ExceptionContextTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed_without_arguments()
    {
        new ExceptionContext();
    }

    public function test_can_be_constructed_wit_exception_arguments()
    {
        new ExceptionContext(new \Exception());
    }

    public function test_get_returns_exception_set_in_constructor()
    {
        $exception = new \Exception();
        $context = new ExceptionContext($exception);
        $this->assertSame($exception, $context->getException());
    }

    public function test_add_exception_sets_exception_if_already_not_set()
    {
        $exception = new \Exception();
        $context = new ExceptionContext();

        $context->addException($exception);
        $this->assertSame($exception, $context->getException());
        $this->assertNull($context->getNextExceptionContext());
    }

    public function test_add_exception_creates_new_exception_context_with_given_exception_if_own_exception_already_set()
    {
        $firstException = new \Exception();
        $context = new ExceptionContext($firstException);
        $secondException = new \Exception();
        $context->addException($secondException);

        $nextContext = $context->getNextExceptionContext();
        $this->assertInstanceOf(ExceptionContext::class, $nextContext);
        $this->assertSame($secondException, $nextContext->getException());

        $this->assertSame($firstException, $context->getException());
    }

    public function test_add_exception_adds_new_context_at_the_end_of_chain()
    {
        $context = new ExceptionContext();
        for ($i=0; $i<5; $i++) {
            $context->addException(new \Exception($i));
        }

        $values = [];
        $i = 0;
        $ctx = $context;
        while ($ctx) {
            $values[] = $ctx->getException()->getMessage();
            $i += 1;
            $ctx = $ctx->getNextExceptionContext();
        }
        $this->assertEquals(5, $i);
        $this->assertEquals(['0', '1', '2', '3', '4'], $values);
    }

    public function test_get_last_exception_return_exception_that_was_last_added()
    {
        $context = new ExceptionContext();
        for ($i=0; $i<5; $i++) {
            $context->addException($expected = new \Exception($i));
        }

        $actual = $context->getLastException();
        $this->assertSame($expected, $actual);
    }
}
