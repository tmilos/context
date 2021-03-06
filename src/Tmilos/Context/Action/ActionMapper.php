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

interface ActionMapper
{
    /**
     * @param Action $action
     *
     * @return Action
     */
    public function __invoke(Action $action);
}
