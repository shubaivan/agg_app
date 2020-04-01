<?php


namespace App\Serializer;


use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use JMS\Serializer\Context;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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
                'method' => 'serializeCustomListToJson',
                'priority' => -915
            ]
        ];
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param ConstraintViolationListInterface $object
     * @param array $type
     * @param Context|null $context
     * @return array
     */
    public function serializeCustomListToJson(
        JsonSerializationVisitor $visitor,
        ConstraintViolationListInterface $object,
        array $type,
        Context $context = null
    )
    {
        [$messages, $violations] = $this->getMessagesAndViolations($object);
        return [
            'title' => 'An error occurred',
            'detail' => $messages ? implode("\n", $messages) : '',
            'violations' => $violations,
        ];
    }

    /**
     * @param ConstraintViolationListInterface $constraintViolationList
     * @return array
     */
    private function getMessagesAndViolations(ConstraintViolationListInterface $constraintViolationList): array
    {
        $violations = $messages = [];
        /** @var ConstraintViolation $violation */
        foreach ($constraintViolationList as $violation) {
            $violations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
                'code' => $violation->getCode(),
            ];
            $propertyPath = $violation->getPropertyPath();
            $messages[] = ($propertyPath ? $propertyPath . ': ' : '') . $violation->getMessage();
        }
        return [$messages, $violations];
    }
}