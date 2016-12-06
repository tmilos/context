<?php
declare(strict_types=1);

namespace Tmilos\Context\Action;

use Tmilos\Context\Action;

interface ActionMapper
{
    /**
     * @param Action $action
     *
     * @return Action
     */
    public function __invoke(Action $action): Action;
}
