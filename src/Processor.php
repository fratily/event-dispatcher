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

use Psr\EventDispatcher\TaskProcessorInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableTaskInterface;
use Psr\EventDispatcher\TaskInterface;

/**
 *
 */
class Processor implements TaskProcessorInterface{

    /**
     * @var ListenerProviderInterface
     */
    private $provider;

    /**
     * Constructor
     *
     * @param   ListenerProviderInterface   $provider
     *  リスナープロバイダーインスタンス
     */
    public function __construct(ListenerProviderInterface $provider){
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TaskInterface $event): TaskInterface{
        foreach($this->provider->getListenersForEvent($event) as $listener){
            try{
                $event  = $listener($event);
            }catch(\Throwable $e){
                throw new Exception\ListenerException(
                    "An error occurred in the event listener.",
                    0,
                    $e
                );
            }

            if($event instanceof TaskInterface){
                $class  = TaskInterface::class;
                throw new \ListenerUnexpectedResultException(
                    "Event listeners executed from the processor must return"
                    . " {$class}."
                );
            }

            if(
                $event instanceof StoppableTaskInterface
                && $event->isPropagationStopped()
            ){
                break;
            }
        }

        return $event;
    }
}