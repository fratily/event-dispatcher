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

use Fratily\Reflection\ReflectionCallable;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 *
 */
class ListenerProvider implements ListenerProviderInterface{

    /**
     * @var Listener[][]
     */
    private $listenersByParameter   = [];

    /**
     * Add event listener.
     *
     * @param callable    $listener
     * @param int         $priority
     * @param string|null $event
     *
     * @return $this
     */
    public function add(
        callable $listener,
        int $priority = 0,
        string $event = null
    ): ListenerProvider{
        if(null !== $event && !class_exists($event)){
            throw new \InvalidArgumentException(
                "class '{$event}' not found."
            );
        }

        $parameter = (new ReflectionCallable($listener))
            ->getReflection()
            ->getParameters()[0] ?? null
        ;

        $type   = null === $parameter ? null : $parameter->getType();
        $class  = $parameter->getClass();

        if(null !== $class){
            return $this->addListener(
                $class->getName(),
                new Listener($listener, $priority)
            );
        }

        if(null === $event){
            throw new \InvalidArgumentException();
        }

        if(null !== $type){
            if("object" !== (string)$type){
                throw new \InvalidArgumentException();
            }
        }

        return $this->addListener(
            $event,
            new Listener($listener, $priority)
        );
    }

    /**
     * Add event listener from listener object.
     *
     * @param string   $event
     * @param Listener $listener
     *
     * @return $this
     */
    protected function addListener(string $event, Listener $listener): ListenerProvider{
        if(!isset($this->listenersByParameter[$event])){
            $this->listenersByParameter[$event] = [];
        }

        $this->listenersByParameter[$event][]   = $listener;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event) : iterable{
        $queue      = new \SplPriorityQueue();
        $eventClass = get_class($event);

        foreach($this->listenersByParameter as $listen => $listeners){
            if($listen === $eventClass || is_subclass_of($eventClass, $listen)){
                foreach($listeners as $listener){
                    $queue->insert($listener->getListener(), $listener->getPriority());
                }
            }
        }

        foreach($queue as $value){
            yield $value;
        }
    }
}
