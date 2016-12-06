<?php

declare(strict_types=1);

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

abstract class AbstractWrappedAction implements Action
{
    /** @var Action */
    private $action;

    /**
     * @param Action $action
     */
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public function execute(Context $context)
    {
        $this->beforeAction($context);
        $this->action->execute($context);
        $this->afterAction($context);
    }

    /**
     * @param Context $context
     */
    abstract protected function beforeAction(Context $context);

    /**
     * @param Context $context
     */
    abstract protected function afterAction(Context $context);
}
