<?php


namespace App\EventListener;

use App\Entity\Product;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class JmsEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'class' => Product::class,
                'method' => 'onPreDeserializeProduct',
            )
        );
    }

    /**
     * @param PreDeserializeEvent $event
     */
    public function onPreDeserializeProduct(PreDeserializeEvent $event)
    {
        $data = $event->getData();
        array_map(function ($key, $value) use (&$data) {
            unset($data[$key]);
            if ($key === 'SKU' || $key === 'EAN' || $key === 'inStock') {
                $key = mb_strtolower($key);
            }

            $data[lcfirst($key)] = ($value === '' ? null : $value);
            return [];
        }, array_keys($data), $data);

        $event->setData($data);
    }
}