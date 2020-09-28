<?php


namespace App\EventListener;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Collection\SearchProducts\AdjacentProduct;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\SlugAbstract;
use App\RepositoryMysql\ColoursRepository;
use Cocur\Slugify\SlugifyInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class JmsEventSubscriber extends SlugApproach implements EventSubscriberInterface
{
    /**
     * @var ColoursRepository ColoursRepository
     */
    private $repository;

    /**
     * JmsEventSubscriber constructor.
     * @param ColoursRepository $repository
     */
    public function __construct(SlugifyInterface $cs, ColoursRepository $repository)
    {
        parent::__construct($cs);
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
                'event' => 'serializer.pre_deserialize',
                'class' => Shop::class,
                'method' => 'onPreDeserializeShop',
            ),
            array(
                'event' => 'serializer.pre_deserialize',
                'class' => Category::class,
                'method' => 'onPreDeserializeCategory',
            ),
            array(
                'event' => 'serializer.pre_deserialize',
                'class' => Brand::class,
                'method' => 'onPreDeserializeBrand',
            ),
            array(
                'event' => 'serializer.post_deserialize',
                'class' => Product::class,
                'method' => 'onPostDeserializeProduct',
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => Category::class,
                'method' => 'onPreSerializeCategory',
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => Shop::class,
                'method' => 'onPreSerializeShop',
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => Brand::class,
                'method' => 'onPreSerializeBrand',
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => Product::class,
                'method' => 'onPreSerializeProduct',
            ),
            array(
                'event' => 'serializer.pre_serialize',
                'class' => AdjacentProduct::class,
                'method' => 'onPreSerializeAdjacentProduct',
            ),
        );
    }

    public function onPreSerializeShop(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreSerializeBrand(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreSerializeProduct(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreSerializeAdjacentProduct(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreSerializeCategory(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreDeserializeBrand(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreDeserializeCategory(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    public function onPreDeserializeShop(ObjectEvent $event)
    {
        $object = $event->getObject();
        $this->applySlug($object);
    }

    /**
     * @param ObjectEvent $event
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onPostDeserializeProduct(ObjectEvent $event)
    {
        /** @var Product $object */
        $object = $event->getObject();
        $this->applySlug($object);

        if (!$object->getGroupIdentity()) {
            $object->setGroupIdentity($object->getIdentityUniqData());
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

    /**
     * @param $object
     */
    private function applySlug($object): void
    {
        if ($object instanceof SlugAbstract
            && !$object->getSlug()
        ) {
            $this->applySlugToEntity($object);
        }
    }
}