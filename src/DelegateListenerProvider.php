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

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 *
 */
class DelegateListenerProvider implements ListenerProviderInterface
{
    /**
     * @var \SplPriorityQueue|ListenerProviderInterface[]
     */
    private $listenerProviders;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->listenerProviders = new \SplPriorityQueue();
    }

    /**
     * Add the listener provider.
     *
     * @param ListenerProviderInterface $provider The listener provider
     * @param int                       $priority The listener provider priority
     *
     * @return $this
     */
    public function add(ListenerProviderInterface $provider, int $priority = 0): DelegateListenerProvider
    {
        if ($this === $provider) {
            throw new \InvalidArgumentException();
        }

        $this->listenerProviders->insert($provider, $priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(object $event) : iterable
    {
        foreach ($this->listenerProviders as $provider) {
            yield from $provider->getListenersForEvent($event);
        }
    }
}
