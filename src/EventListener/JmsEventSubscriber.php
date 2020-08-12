<?php


namespace App\EventListener;

use App\Entity\Product;
use App\RepositoryMysql\ColoursRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class JmsEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ColoursRepository ColoursRepository
     */
    private $repository;

    /**
     * JmsEventSubscriber constructor.
     * @param ColoursRepository $repository
     */
    public function __construct(ColoursRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'class' => Product::class,
                'method' => 'onPreDeserializeProduct',
            ),
            array(
                'event' => 'serializer.post_deserialize',
                'class' => Product::class,
                'method' => 'onPostDeserializeProduct',
            )
        );
    }

    /**
     * @param ObjectEvent $event
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onPostDeserializeProduct(ObjectEvent $event)
    {
        /** @var Product $object */
        $object = $event->getObject();
        if (!$object->getGroupIdentity()) {
            $object->setGroupIdentity($object->getSku());
        }

        if ($object->getShop() == 'COS') {
            $object->setProductUrl(urldecode($object->getProductUrl()));
            $object->setImageUrl(urldecode($object->getImageUrl()));
            $object->setTrackingUrl(urldecode($object->getTrackingUrl()));
        }

        $extras = $object->getExtras();
        if (is_array($extras) && isset($extras[Product::COLOUR]) && !isset($extras[Product::OWN_COLOUR])) {
            $colour = $this->repository
                ->findOneByOriginalColorField($extras[Product::COLOUR]);
            if ($colour) {
                $extras[Product::OWN_COLOUR] = $colour->getSubstituteColor();
                $object->setExtras($extras);
            } else {
                $extras[Product::OWN_COLOUR] = 'Flerfärgat';
                $object->setExtras($extras);
            }
        }
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