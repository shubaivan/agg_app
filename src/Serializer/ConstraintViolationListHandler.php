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
                'method' => 'serializeListToJson',
            ]
        ];
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param ConstraintViolationList $constraintViolationList
     * @param array $type
     * @param Context|null $context
     * @return int
     */
    public function serializeListToJson(
        JsonSerializationVisitor $visitor,
        ConstraintViolationList $constraintViolationList,
        array $type,
        Context $context = null
    )
    {
        $t = 1;

        return 1;
    }
}