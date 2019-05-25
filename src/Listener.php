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

/**
 *
 */
class Listener{

    /**
     * @var callable
     */
    private $listener;

    /**
     * @var int
     */
    private $priority;

    /**
     * Constructor.
     *
     * @param callable $listener
     * @param int      $priority
     */
    public function __construct(callable $listener, int $priority){
        $this->listener = $listener;
        $this->priority = $priority;
    }

    /**
     * Get listener.
     *
     * @return callable
     */
    public function getListener(): callable{
        return $this->listener;
    }

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority(): int{
        return $this->priority;
    }
}