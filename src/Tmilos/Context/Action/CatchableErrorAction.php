<?php

/*
 * This file is part of the Tmilos/Context package.
 *
 * (c) Milos Tomic <tmilos@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Context;
use Tmilos\Context\Context\ExceptionContext;

class CatchableErrorAction implements Action
{
    /** @var Action */
    protected $mainAction;

    /** @var Action */
    protected $errorAction;

    /**
     * @param Action $mainAction
     * @param Action $errorAction
     */
    public function __construct(Action $mainAction, Action $errorAction)
    {
        $this->mainAction = $mainAction;
        $this->errorAction = $errorAction;
    }

    /**
     * @param Context $context
     *
     * @return void
     */
    public function execute(Context $context)
    {
        try {
            $this->mainAction->execute($context);
        } catch (\Exception $ex) {
            /** @var ExceptionContext $exceptionContext */
            $exceptionContext = $context->getOrCreate('exception', ExceptionContext::class);
            $exceptionContext->addException($ex);

            $this->errorAction->execute($context);
        }
    }
}
