<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * PHP version 5.3.0
 * 
 * @author    ____author____ 
 * @copyright ____copyright____
 * @license   ____license____
 * @link      https://github.com/max-shamaev/swarm
 * @since     1.0.0
 */

/**
 * WARNING! REQUIRE git clone https://github.com/tnc/php-amqplib.git into directory examples/amqp
 */

require_once __DIR__ . '/../src/Swarm/Loader.php';
require_once __DIR__ . '/amqp/amqp.inc';

class Worker extends \Swarm\Worker\AMQP
{
    protected function getAMQPServerSettings()
    {
        return array(
            'host'     => 'localhost',
            'port'     => 5672,
            'user'     => 'guest',
            'password' => 'guest',
            'vhost'    => '/',
        );
    }

    protected function getHandlers()
    {
        return array(
            new Hnd,
        );
    }
}

class Hnd extends \Swarm\Worker\AMQP\Handler
{
    protected $listeners = array(
        array('msgs'),
    );

    public function handle(\AMQPMessage $message, $queue, $tag)
    {
        var_dump($queue, $tag, $message->body);
        $this->sendAck($message);
    }
}

\Swarm\Manager::getInstance()
    ->setPlanner(
        \Swarm\Planner::getInstance()
            ->addWorker('Worker')
    )
    ->run();
