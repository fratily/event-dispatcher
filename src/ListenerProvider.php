<?php
/**
 * FratilyPHP Event Dispatcher
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 *
 */
class ListenerProvider implements ListenerProviderInterface{

    /**
     * @var ListenerIdGeneratorInterface
     */
    private $idGenerator;

    /**
     * @var Listener[]
     */
    private $listeners  = [];

    /**
     * Constructor.
     *
     * @param   ListenerIdGeneratorInterface    $idGenerator
     */
    public function __construct(ListenerIdGeneratorInterface $idGenerator){
        $this->idGenerator  = $idGenerator ?? new ListenerIdGenerator();
    }

    /**
     * Get ListenerIdGeneratorInterface instance.
     *
     * @return  ListenerIdGeneratorInterface
     */
    protected function getListenerIdGenerator(): ListenerIdGeneratorInterface{
        return $this->idGenerator;
    }

    /**
     * Get Listener instance.
     *
     * If the listener is not registered, get the newly registered one.
     *
     * @param   callable    $listener
     *
     * @return  Listener
     */
    public function listener(callable $listener): Listener{
        $id = $this->getListenerIdGenerator()->generate($listener);

        if(!array_key_exists($id, $this->listeners)){
            if(
                is_array($listener)
                && is_string($listener[0])
                && false !== strpos($listener[1], "::")
            ){
                // ["SubClass", "parent::foo"] ["Class", self::bar]
                // If allow this, will have to use call_user_func without fail.
                // can not call the listener like $listener($event).
                throw new \InvalidArgumentException();

                // Other solution: Correct the class name with "self ::" or "parent ::"
            }

            $this->listeners[$id]   = new Listener($listener);
        }

        return $this->listeners[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event) : iterable{
        $eventClass = get_class($event);

        foreach($this->listeners as $listener){
            if($listener->isListenEventClass($eventClass)){
                yield $listener->getListener();
            }
        }
    }
}