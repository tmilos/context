<?php
declare(strict_types=1);

namespace Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Dumpable;

interface CompositeAction extends Action, Dumpable
{
    /**
     * @param Action $action
     *
     * @return void
     */
    public function add(Action $action);

    /**
     * @param ActionMapper|callable $mapper
     *
     * @return void
     */
    public function map($mapper);

    /**
     * @return Action[]
     */
    public function getActions() : array;
}
