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
    public function getActions(): array;
}
