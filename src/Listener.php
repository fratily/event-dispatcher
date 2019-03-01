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
    private $callback;

    /**
     * @var bool[]
     */
    private $listenEvents   = [];

    /**
     * Get ReflectionFunction or ReflectionMethod
     *
     * @param   callable    $callable
     *
     * @return  \ReflectionFunctionAbstract
     */
    private static function getReflection(callable $callable): \ReflectionFunctionAbstract{
        try{
            if(is_string($callable)){
                if(false === strpos($callable, "::")){
                    return new \ReflectionFunction($callable);
                }

                return new \ReflectionMethod($callable);
            }

            if(is_object($callable)){
                return new \ReflectionMethod($callable, "__invoke");
            }

            if(!is_array($callable)){
                throw new \LogicException();
            }

            if(false !== strpos($callable[1], "::")){
                $callable[1]    = explode("::", $callable[1], 2)[1];
            }

            return new \ReflectionMethod($callable[0], $callable[1]);
        }catch(\ReflectionException $e){
            throw new \LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get relation class name list.
     *
     * @param   \ReflectionClass    $class
     *
     * @return  string[]
     */
    private static function getRelationClasses(\ReflectionClass $class): array{
        $classes    = array_values($class->getInterfaceNames());

        do{
            $classes[]  = $class->getName();
        }while(false !== ($class = $class->getParentClass()));

        return $classes;
    }

    /**
     * Constructor
     *
     * @param   callable    $listener
     *  リスナーのコールバック
     */
    public function __construct(callable $listener){
        $function   = self::getReflection($listener);

        if(0 === $function->getNumberOfParameters()){
            throw new \InvalidArgumentException();
        }

        if(1 < $function->getNumberOfRequiredParameters()){
            throw new \InvalidArgumentException();
        }

        $parameter  = $function->getParameters()[0];

        if(!$parameter->hasType() || $parameter->getType()->isBuiltin()){
            throw new \InvalidArgumentException();
        }

        try{
            $type   = $parameter->getClass();
        }catch(\ReflectionException $e){
            throw new \LogicException(
                sprintf("%s (in %s %d)", $e->getMessage(), $function->getFileName(), $function->getStartLine()),
                $e->getCode(),
                $e
            );
        }

        $this->callback     = $listener;
        $this->listenEvents = [];

        foreach(self::getRelationClasses($type) as $listenEvent){
            $this->listenEvents[$listenEvent]   = true;
        }
    }

    /**
     * Get callback.
     *
     * @return  callable
     */
    public function getListener(): callable{
        return $this->callback;
    }

    /**
     * Get listen event names.
     *
     * @return  string[]
     */
    public function getListenEvents(): array{
        return array_keys(array_filter($this->listenEvents));
    }

    /**
     * Is listen event class.
     *
     * @param   string  $class
     *
     * @return  bool
     */
    public function isListenEventClass(string $class): bool{
        return array_key_exists($class, $this->listenEvents)
            && $this->listenEvents[$class]
        ;
    }

    /**
     * Add listen event.
     *
     * @param   string  $class  Listen event class.
     * @param   bool    $withRelationClass  Add super class and implements interfaces.
     *
     * @return  $this
     */
    public function listen(string $class, bool $withRelationClass = true){
        try{
            $class = new \ReflectionClass($class);
        }catch(\ReflectionException $e){
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $this->listenEvents[$class->getName()]  = true;

        if($withRelationClass){
            foreach(self::getRelationClasses($class) as $relationClass){
                $this->listenEvents[$relationClass] = true;
            }
        }

        return $this;
    }

    /**
     * Remove listen event.
     *
     * @param   string  $class  Unlisten event class.
     * @param   bool    $withRelationClass  Remove super class and implements interfaces.
     *
     * @return  $this
     */
    public function unlisten(string $class, bool $withRelationClass = true){
        try{
            $class = new \ReflectionClass($class);
        }catch(\ReflectionException $e){
            throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        $this->listenEvents[$class->getName()]  = false;

        if($withRelationClass){
            foreach(self::getRelationClasses($class) as $relationClass){
                $this->listenEvents[$relationClass] = false;
            }
        }

        return $this;
    }
}