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
interface ListenerIdGeneratorInterface{

    /**
     * Generate listener id.
     *
     * The same value must be returned for the same input.
     *
     * @param   callable    $listener   Listener value.
     *
     * @return  string
     */
    public function generate(callable $listener): string;
}