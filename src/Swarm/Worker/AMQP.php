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

namespace Swarm\Worker;

/**
 * AMQP client 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class AMQP extends \Swarm\Worker
{
    /**
     * Connection
     *
     * @var   \AMQPConnection
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $connection;

    /**
     * Channel
     *
     * @var   \AMQPChannel
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $channel;

    /**
     * Worker arguments
     *
     * @var   mixed
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $arguments;

    /**
     * Get AMQP server settings 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract protected function getAMQPServerSettings();

    /**
     * Get handlers 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract protected function getHandlers();

    /**
     * Run worker
     *
     * @param mixed $arguments Arguments
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function run($arguments = null)
    {
        $this->arguments = $arguments;

        $this->initializeConnection();

        $this->setupChannel();
        $this->assignHandlers();

        while ($this->isAlive() && count($this->channel->callbacks)) {
            $this->channel->wait();
            pcntl_signal_dispatch();
        }
    }

    /**
     * Destructor
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __destruct()
    {
        $this->finalizeConnection();
    }

    /**
     * Finalize connection
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function finalizeConnection()
    {
        if (isset($this->channel)) {
            $this->channel->close();
        }

        if (isset($this->connection)) {
            $this->connection->close();
        }
    }

    /**
     * Initialize connection
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function initializeConnection()
    {
        $config = $this->getAMQPServerSettings();

        $this->connection = new \AMQPConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * Setup channel 
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function setupChannel()
    {
        $this->channel->queue_declare($this->getQueue(), false, true, false, false);
        $this->channel->exchange_declare($this->getExchange(), $this->getType(), false, true, false);
        $this->channel->queue_bind($this->getQueue(), $this->getExchange());

    }

    /**
     * Assign channel handlers
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assignHandlers()
    {
        foreach ($this->getHandlers() as $handler) {
            $this->assignHandler($handler);
        }
    }

    /**
     * Assign channel handler 
     * 
     * @param \Swarm\Worker\AMQP\Handler $handler Handler
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function assignHandler(\Swarm\Worker\AMQP\Handler $handler)
    {
        $this->channel->basic_consume(
            $this->getQueue(),
            $handler::getTag(),
            false,
            false,
            false,
            false,
            $handler::getCallback()
        );
    }

    /**
     * Get channel queue 
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getQueue()
    {
        return 'msgs';
    }

    /**
     * Get channel exchange 
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getExchange()
    {

        return 'swarm';
    }

    /**
     * Get channel type 
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getType()
    {
        return 'direct';
    }
}

