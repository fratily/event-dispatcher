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
class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var Listener[][]
     */
    private $listenersByParameter = [];

    /**
     * Add the event listener.
     *
     * @param callable $listener The listener
     * @param int      $priority The listener priority
     *
     * @return $this
     */
    public function add(callable $listener, int $priority = 0): ListenerProvider
    {
        $parameter = (new ReflectionCallable($listener))
            ->getReflection()
            ->getParameters()[0] ?? null
        ;

        if (null === $parameter || null === $parameter->getClass()) {
            throw new \InvalidArgumentException();
        }

        $eventClass = $parameter->getClass();

        if (!isset($this->listenersByParameter[$eventClass->getName()])) {
            $this->listenersByParameter[$eventClass->getName()] = [];
        }

        $this->listenersByParameter[$eventClass->getName()][] = new Listener($listener, $priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event) : iterable
    {
        $listenerPriorityQueue = new \SplPriorityQueue();
        $eventClass            = get_class($event);
        $currentClass          = $eventClass;
        $listenClasses         = class_implements($eventClass);

        do {
            $listenClasses[] = $currentClass;
        } while (null !== ($currentClass = get_parent_class($currentClass)));

        foreach ($listenClasses as $listenClass) {
            if (!isset($this->listenersByParameter[$listenClass])) {
                continue;
            }

            foreach ($this->listenersByParameter[$listenClass] as $listener) {
                $listenerPriorityQueue->insert(
                    $listener->getListener(),
                    $listener->getPriority()
                );
            }
        }

        foreach ($listenerPriorityQueue as $listener) {
            yield $listener;
        }
    }
}
