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
namespace Fratily\EventDispatcher\Event;

/**
 *
 */
class CaughtException{

    /**
     * @var \Throwable
     */
    private $caught;

    /**
     * Constructor.
     *
     * @param   \Throwable  $caught Caught exception.
     */
    public function __construct(\Throwable $caught){
        $this->caught   = $caught;
    }

    /**
     * Get caught exception(error).
     *
     * @return  \Throwable
     */
    public function getCaught(){
        return $this->caught;
    }
}