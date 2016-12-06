<?php
declare(strict_types=1);

namespace Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Context;

class NullAction implements Action
{
    public function execute(Context $context)
    {

    }
}
