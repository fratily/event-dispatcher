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
class ListenerIdGenerator implements ListenerIdGeneratorInterface{

    /**
     * @inheritdoc
     */
    public function generate(callable $listener): string{
        if(is_object($listener)){
            $listener   = [$listener, "__invoke"];
        }

        if(is_array($listener)){
            $listener   = sprintf(
                "%s::%s",
                is_object($listener[0])
                    ? (spl_object_hash($listener[0]) . "@object")
                    : $listener[0]
                ,
                $listener[1]
            );
        }

        if(!is_string($listener)){
            throw new \LogicException();
        }

        return md5($listener);
    }
}