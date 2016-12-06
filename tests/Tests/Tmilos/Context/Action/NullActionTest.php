<?php

namespace Tests\Tmilos\Context\Action;

use Tmilos\Context\Action\NullAction;
use Tmilos\Context\Context;

class NullActionTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_executed()
    {
        $action = new NullAction();
        $action->execute($this->createMock(Context::class));
    }
}
