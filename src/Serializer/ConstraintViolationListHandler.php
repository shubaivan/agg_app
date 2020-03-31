<?php


namespace App\Serializer;


use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Validator\ConstraintViolationList;
use JMS\Serializer\Context;

class ConstraintViolationListHandler implements SubscribingHandlerInterface
{
    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => ConstraintViolationList::class,
                'method' => 'serializeListTojson',
            ]
        ];
    }

    public function serializeListTojson(
        JsonSerializationVisitor $visitor,
        $constraintViolationList,
        array $type,
        Context $context = null
    )
    {
        $t = 1;

        return 1;
    }
}