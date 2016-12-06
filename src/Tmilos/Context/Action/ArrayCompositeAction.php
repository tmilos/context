<?php
declare(strict_types=1);

namespace Tmilos\Context\Action;

use Tmilos\Context\Action;
use Tmilos\Context\Context;
use Tmilos\Context\Dumpable;

class ArrayCompositeAction implements CompositeAction
{
    /**
     * @var Action[]
     */
    private $actions = [];

    /**
     * @param Action[] $actions
     */
    public function __construct(array $actions = [])
    {
        foreach ($actions as $action) {
            $this->add($action);
        }
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function execute(Context $context)
    {
        foreach ($this->actions as $action) {
            $action->execute($context);
        }
    }

    public function add(Action $action)
    {
        $this->actions[] = $action;
    }

    public function map($mapper)
    {
        foreach ($this->actions as $k=>$action) {
            $this->actions[$k] = $mapper($action);
        }
    }

    public function dump(): array
    {
        $result = [];
        foreach ($this->actions as $action) {
            if ($action instanceof Dumpable) {
                $result = array_merge($result, $action->dump());
            } else {
                $result = array_merge($result, [get_class($action) => []]);
            }
        }

        return [
            get_class($this) => $result,
        ];
    }
}
