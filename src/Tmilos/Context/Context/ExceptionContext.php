<?php

/*
 * This file is part of the Tmilos/Context package.
 *
 * (c) Milos Tomic <tmilos@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tmilos\Context\Context;

class ExceptionContext extends ArrayContext
{
    /** @var \Exception */
    protected $exception;

    /** @var ExceptionContext|null */
    protected $nextExceptionContext;

    /**
     * @param \Exception|null $exception
     */
    public function __construct(\Exception $exception = null)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return \Exception|null
     */
    public function getLastException()
    {
        if (!$this->nextExceptionContext) {
            return $this->exception;
        }

        return $this->nextExceptionContext->getLastException();
    }

    /**
     * @return ExceptionContext|null
     */
    public function getNextExceptionContext()
    {
        return $this->nextExceptionContext;
    }

    /**
     * @param \Exception $exception
     *
     * @return ExceptionContext Where given exception was stored
     */
    public function addException(\Exception $exception)
    {
        if ($this->exception) {
            if (!$this->nextExceptionContext) {
                $this->nextExceptionContext = new static($exception);

                return $this->nextExceptionContext;
            } else {
                return $this->nextExceptionContext->addException($exception);
            }
        }

        $this->exception = $exception;

        return $this;
    }
}
