<?php
declare(strict_types=1);

namespace Tmilos\Context;

interface Dumpable
{
    /**
     * @return array
     */
    public function dump() : array;
}
