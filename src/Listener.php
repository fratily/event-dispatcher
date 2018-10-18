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
use Psr\EventDispatcher\EventInterface;

/**
 *
 */
class Listener{

    /**
     * @var callable
     */
    private $listener;

    /**
     * @var string
     */
    private $allowEvent;

    /**
     * Constructor
     *
     * @param   callable    $listener
     *  リスナーのコールバック
     */
    public function __construct(callable $listener){
        $reflection = (new ReflectionCallable($listener))->getReflection();

        if(0 === count($reflection->getParameters())){
            throw new \InvalidArgumentException(
                "Listener must have one parameter."
            );
        }

        if(
            1 < count($reflection->getParameters())
            && !$reflection->getParameters()[1]->isDefaultValueAvailable()
        ){
            throw new \InvalidArgumentException(
                "More than one parameter is specified, and the second and"
                . " subsequent parameters are not optional."
            );
        }

        $parameter  = $reflection->getParameters()[0];

        if(!$parameter->hasType() || $parameter->getType()->isBuiltin()){
            throw new \InvalidArgumentException(
                "For the listener's first parameter, must specify the class"
                . " name type."
            );
        }

        try{
            $class  = $parameter->getClass();
        }catch(\ReflectionException $e){
            throw new \InvalidArgumentException(
                "A class type that can not resolved is specified as the first"
                    . " parameter of the listener."
                ,
                0,
                $e
            );
        }

        if(!$class->implementsInterface(EventInterface::class)){
            $event  = EventInterface::class;
            throw new \InvalidArgumentException(
                "The listener's first parameter must request a class"
                . " implementhing {$event}."
            );
        }

        $this->listener     = $listener;
        $this->allowEvent   = $class->getName();
    }

    /**
     * リスナーのコールバックを取得する
     *
     * @return  callable
     */
    public function getListener(){
        return $this;
    }

    /**
     * リスナーが要求するイベントクラスを取得する
     *
     * @return  string
     */
    public function getAllowdEvent(){
        return $this->allowEvent;
    }
}