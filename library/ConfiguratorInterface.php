<?php

namespace Guide42\Choclo;

/**
 * Configurator interface.
 */
interface ConfiguratorInterface
{
    /**
     * Default phase to add actions.
     */
    const PHASE_DEFAULT = 0;

    /**
     * Add an action to configure something in the future.
     *
     * @param array|string $key       Configuration path
     * @param callable     $configure Callable to configure the key
     */
    function register($key, callable $configure, $phase=self::PHASE_DEFAULT);

    /**
     * Discard actions.
     */
    function rollback();

    /**
     * Execute actions or throw an exception on error.
     *
     * @throws RuntimeException
     * @return boolean
     */
    function commit();
}