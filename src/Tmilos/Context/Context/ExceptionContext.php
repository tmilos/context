<?php
declare(strict_types=1);

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
    public function getException() : \Exception
    {
        return $this->exception;
    }

    /**
     * @return \Exception|null
     */
    public function getLastException()
    {
        if (null == $this->nextExceptionContext) {
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
            if (null == $this->nextExceptionContext) {
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
