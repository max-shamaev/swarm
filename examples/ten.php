<?php

require_once __DIR__ . '/../src/Swarm/Loader.php';

class Worker extends \Swarm\Worker\Permanent 
{
    const SLEEP_TIME = 5;

    /**
     * Periodic work
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function work()
    {
        echo ' ' . ($this->arguments[0] + $this->arguments[1]) . ' ';
    }
}

\Swarm\Manager::getInstance()
    ->setPlanner(
        \Swarm\Planner::getInstance()
            ->addWorker('Worker', array(1, 2), 10)
    )
    ->run();
