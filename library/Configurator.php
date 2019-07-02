<?php

namespace choclo;

class Configurator implements \ArrayAccess
{
    private/* \SplPriorityQueue */ $queue;
    private/* integer */ $order = PHP_INT_MAX;

    function __construct(string $path, array $queue=[]) {
        $this->queue = new \SplPriorityQueue;
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        
        foreach ($queue as $key => $configure) {
            $this->offsetSet($key, $configure);
        }
    }

    function offsetExists(string $key): bool {
        //
    }

    function offsetGet(string $key): \Generator {
        //
    }

    function offsetSet(string $key, callable $configure): void {
        $path = dirname($key);
        $key = basename($key);

        $this->queue->insert(
            [$key, $configure],
            [$path, --$this->order]
        );
    }

    function offsetUnset(string $key): void {
        //
    }

    function resolve() {
        $output = $unique = $conflicts = [];

        foreach ($this->queue as $pos => $info) {
            $path = $info['priority'][0];
            $key = $info['data'][0];
            $fn = $info['data'][1];

            if ($key === null) {
                $output[] = [null, $fn];
            } else {
                $unique[$key] = $unique[$key] ?? [];
                $unique[$key][] = $info;
            }
        }

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

    function commit() {
        foreach ($this->resolve() as [$key, $fn]) {
            call_user_func($fn);
        }

        $this->queue = [];
        $this->order = PHP_INT_MAX;
    }
}