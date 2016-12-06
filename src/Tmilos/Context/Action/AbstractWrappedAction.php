<?php
declare(strict_types=1);

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
