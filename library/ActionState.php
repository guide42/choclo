<?php

namespace Guide42\Choclo;

use Guide42\Choclo\Exception\ExecutionException;
use Guide42\Choclo\Exception\ConflictException;

class ActionState
{
    private $queue = array(); // by phase
    private $order = PHP_INT_MAX;

    public function reset() {
        $this->queue = array();
        $this->order = PHP_INT_MAX;
    }

    public function push($phase, $key, callable $fn, $path, $priority=0) {
        if (array_key_exists($phase, $this->queue) === false) {
            $this->queue[$phase] = new \SplPriorityQueue;
            $this->queue[$phase]->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        }

        $this->queue[$phase]->insert(
            array($key, $fn),
            array($path, $priority, --$this->order)
        );
    }

    public function exec() {
        foreach ($this->resolve() as $action) {
            list($key, $fn) = $action;

            try {
                call_user_func($fn);
            } catch (\Exception $e) {
                $msg = 'An error occurred during execution'
                     . ' of a configuration action';

                throw new ExecutionException($msg, null, $e, $key, $fn);
            }
        }
    }

    protected function resolve() {
        foreach ($this->queue as $phase => $actions) {
            $output = array();
            $unique = array();

            foreach ($actions as $pos => $info) {
                $path = $info['priority'][0];
                $key = $info['data'][0];
                $fn = $info['data'][1];

                if ($key === null) {
                    $output[] = array($key, $fn);
                    continue;
                }

                if (array_key_exists($key, $unique) === false) {
                    $unique[$key] = array();
                }

                $unique[$key][] = $info;
            }

            $conflicts = array();

            foreach ($unique as $actions) {
                $base = array_pop($actions);
                $path = $base['priority'][0];

                $output[] = $base['data'];

                foreach ($actions as $info) {
                    $curr = $info['priority'][0];
                    if ($curr === $path || substr($curr, 0, strlen($path)) !== $path) {
                        if (array_key_exists($key, $conflicts) === false) {
                            $conflicts[$key] = array($base);
                        }

                        $conflicts[$key][] = $info;
                    }
                }
            }

            if (count($conflicts) > 0) {
                $msg = sprintf('%d conflicting configuration actions',
                    count($conflicts)
                );

                throw new ConflictException($msg, null, null, $conflicts);
            }

            foreach ($output as $action) {
                yield $action;
            }
        }
    }
}