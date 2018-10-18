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

use Psr\EventDispatcher\MessageNotifierInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\MessageInterface;

/**
 *
 */
class Notifier implements MessageNotifierInterface{

    /**
     * @var ListenerProviderInterface
     */
    private $provider;

    /**
     * @var \Throwable[]
     */
    private $lastErrors = [];

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
     * {@iheritdoc}
     */
    public function notify(MessageInterface $event): void{
        $this->lastErrors   = [];

        foreach($this->provider->getListenersForEvent($event) as $listener){
            try{
                $listener(clone $event);
            }catch(\Throwable $e){
                $this->lastErrors[] = $e;
            }
        }
    }

    /**
     * 直近のイベントを発火した際に発生した例外とエラーのリストを取得する
     *
     * @return  \Throwable[]
     */
    public function getLastErrors(){
        return $this->lastErrors;
    }
}