<?php
declare(strict_types=1);

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
