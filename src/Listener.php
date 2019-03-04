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

use Fratily\EventDispatcher\Exception\InvalidParameterTypeException;

/**
 *
 */
class Listener{

    const DEFAULT_PRIORITY  = 0;
    const DEFAULT_ENABLED   = true;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $listen;

    /**
     * @var int
     */
    private $priority   = self::DEFAULT_PRIORITY;

    /**
     * @var bool
     */
    private $isEnabled  = self::DEFAULT_ENABLED;

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
     * Constructor
     *
     * @param   callable    $listener   Listener callback.
     * @param   int $priority   Event execute priority.
     *
     * @throws  Exception\TooFewParametersException
     * @throws  Exception\TooManyRequiredParametersException
     * @throws  Exception\InvalidParameterTypeException
     */
    public function __construct(callable $listener, int $priority = self::DEFAULT_PRIORITY){
        $function   = self::getReflection($listener);

        if(0 === $function->getNumberOfParameters()){
            throw new Exception\TooFewParametersException(
                "Listeners must have only one required parameter."
            );
        }

        if(1 < $function->getNumberOfRequiredParameters()){
            throw new Exception\TooManyRequiredParametersException(
                "Listeners must have only one required parameter."
            );
        }

        $parameter  = $function->getParameters()[0];

        if(!$parameter->hasType() || $parameter->getType()->isBuiltin()){
            throw new Exception\InvalidParameterTypeException(
                "The listener's first argument must explicitly state the event class type."
            );
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

        $this->callback = $listener;
        $this->listen   = $type->getName();
        $this->priority = $priority;
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
     * Get listen event class name.
     *
     * @return  string
     */
    public function getListenEventClass(): ?string{
        return $this->isEnabled ? $this->listen : null;
    }

    /**
     * Get event subscribe priority.
     *
     * @return  int
     */
    public function getPriority(): int{
        return $this->priority;
    }

    /**
     * Set priority.
     *
     * @param   int $priority
     *
     * @return  $this
     */
    public function setPriority(int $priority): self{
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get is enabled.
     *
     * @return  bool
     */
    public function isEnabled(): bool{
        return $this->isEnabled;
    }

    /**
     * Set enabled.
     *
     * @return  $this
     */
    public function enable(): self{
        $this->isEnabled    = true;

        return $this;
    }

    /**
     * Set disabled.
     *
     * @return  $this
     */
    public function disable(): self{
        $this->isEnabled    = false;

        return $this;
    }
}