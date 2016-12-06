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

namespace Tmilos\Context;

interface Action
{
    /**
     * @param Context $context
     *
     * @return void
     */
    public function execute(Context $context);
}
