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

use Psr\EventDispatcher\EventInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 *
 */
class ListenerProvider implements ListenerProviderInterface{

    /**
     * @var callable[]
     */
    private $listeners  = [];

    /**
     * @var string[][]
     */
    private $relations  = [];

    protected static function getSuperClasses(string $class){
        if(!class_exists($class)){
            throw new \InvalidArgumentException;
        }

        $result = [$class];
        $parent = $class;

        while(false !== ($parent = get_parent_class($parent))){
            $result[]   = $parent;
        }

        $result = array_map($result, class_implements($class));

        return array_unique($result);
    }

    /**
     * イベントリスナーを追加する
     *
     * @param   callable    $listener
     *  イベントリスナーとして機能するコールバック
     *
     * @return  $this
     */
    public function addListener(callable $listener){
        $id         = bin2hex(random_bytes(5));
        $listener   = new Listener($listener);

        foreach(static::getSuperClasses($listener->getAllowdEvent()) as $class){
            if(!array_key_exists($class, $this->relations)){
                $this->relations[$class]    = [];
            }

            $this->relations[$class][]  = $id;
        }

        $this->listeners[$id]   = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getListenersForEvent(EventInterface $event): iterable{
        $idList = [];

        foreach(static::getSuperClasses(get_class($event)) as $class){
            if(!array_key_exists($class, $this->relations)){
                continue;
            }

            foreach($this->relations[$class] as $id){
                if(
                    array_key_exists($id, $idList)
                    || !array_key_exists($id, $this->listeners)
                ){
                    continue;
                }

                $idList[$id]    = true;

                yield $this->listeners[$id]->getListener();
            }
        }
    }
}