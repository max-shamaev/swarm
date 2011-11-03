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

namespace Swarm\Worker\AMQP;

/**
 * AMQP handler 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
abstract class Handler extends \Swarm\Base\Singleton
{
    /**
     * Process message
     *
     * @param \AMQPMessage $message Message
     *
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    abstract public function handle(\AMQPMessage $message);

    /**
     * Get consumer tag
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    static public function getTag()
    {
        return 'consumer';
    }

    /**
     * Get callback
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    static public function getCallback()
    {
        return get_called_class() . '::handleStatic';
    }

    /**
     * Process message (callback)
     *
     * @param \AMQPMessage $message Message
     *
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    static public function handleStatic(\AMQPMessage $message)
    {
        return static::getInstance()->handle($message);
    }

}

