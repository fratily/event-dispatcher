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
class CaughtThrowableObjectEvent
{
    /**
     * @var \Throwable
     */
    private $caught;

    /**
     * Constructor.
     *
     * @param \Throwable $caught The Throwable object
     */
    public function __construct(\Throwable $caught)
    {
        $this->caught = $caught;
    }

    /**
     * Returns The Throwable object.
     *
     * @return \Throwable
     */
    public function getCaughtThrowableObject(): \Throwable
    {
        return $this->caught;
    }
}
