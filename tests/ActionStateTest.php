<?php

namespace Guide42\ChocloTest;

use Guide42\Choclo\ActionState;

class ActionStateTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $state = new ActionState();
        $state->push(0, 'set', function() {}, '/');
    }

    public function testExecute()
    {
        $exec = 0;

        $state = new ActionState();
        $state->push(0, 'set', function() use (&$exec) { $exec++; }, '/');
        $state->exec();

        $this->assertEquals(1, $exec);
    }

    public function testExecuteOverride()
    {
        $exec = 0;

        $state = new ActionState();
        $state->push(0, 'set', function() use (&$exec) { $exec++; }, '/1');
        $state->push(0, 'set', function() use (&$exec) { $exec++; }, '/');
        $state->exec();

        $this->assertEquals(1, $exec);
    }

    /**
     * @expectedException Guide42\Choclo\Exception\ConflictException
     */
    public function testExecuteConflict()
    {
        $state = new ActionState();
        $state->push(0, 'set', function() {}, '/1');
        $state->push(0, 'set', function() {}, '/2');
        $state->exec();
    }

    /**
     * @expectedException Guide42\Choclo\Exception\ConflictException
     */
    public function testExecuteConflictSamePath()
    {
        $state = new ActionState();
        $state->push(0, 'set', function() {}, '/');
        $state->push(0, 'set', function() {}, '/');
        $state->exec();
    }

    public function testExecuteByPhase()
    {
        $exec = 0;

        $state = new ActionState();
        $state->push(0, 'set', function() use (&$exec) { $exec++; }, '/');
        $state->push(1, 'set', function() use (&$exec) { $exec++; }, '/');
        $state->exec();

        $this->assertEquals(2, $exec);
    }

    public function testExcecuteNullKey()
    {
        $executed = 0;

        $state = new ActionState();
        $state->push(0, null, function() use (&$exec) { $exec++; }, '/1');
        $state->push(0, null, function() use (&$exec) { $exec++; }, '/2');
        $state->exec();

        $this->assertEquals(2, $exec);
    }
}