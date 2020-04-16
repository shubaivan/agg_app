<?php

namespace App\Services;

use App\Exception\EntityValidatorException;
use App\Exception\ValidatorException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ObjectsHandler
{
    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * @var ValidatorInterface
     */
    private $symfonyValidator;

    /**
     * ObjectsHandler constructor.
     * @param SerializerInterface $jmsSerializer
     * @param ValidatorInterface $symfonyValidator
     */
    public function __construct(
        SerializerInterface $jmsSerializer,
        ValidatorInterface $symfonyValidator
    )
    {
        $this->jmsSerializer = $jmsSerializer;
        $this->symfonyValidator = $symfonyValidator;
    }

    /**
     * @param $deSerializedData
     * @param $class
     * @param array $groups
     * @param string $type
     * @param bool $validate
     *
     * @return mixed
     * @throws ValidatorException
     */
    public function handleObject(
        $deSerializedData,
        $class,
        $groups = [],
        $type = 'json',
        bool $validate = true
    )
    {
        if (is_array($deSerializedData)) {
            $deSerializedData = $this->getJmsSerializer()
                ->serialize($deSerializedData, 'json');
        }

        $deserializationContext = null;
        $validateGroups = [];
        if ($groups) {
            $deserializationContext = DeserializationContext::create()->setGroups($groups);
            $validateGroups = $groups;
        }

        $dataValidate = $this->getJmsSerializer()
            ->deserialize(
                $deSerializedData,
                $class,
                $type,
                $deserializationContext
            );

        if ($validate) {
            $this->validateEntity($dataValidate, $validateGroups);
        }

        return $dataValidate;
    }

    /**
     * @param EntityValidatorException $entity
     * @param array $validateGroups
     *
     * @throws ValidatorException
     */
    public function validateEntity(
        $entity,
        array $validateGroups = []
    )
    {
        $validateGroups = $validateGroups ? $validateGroups : null;
        $errors = $this->getSymfonyValidator()
            ->validate($entity, null, $validateGroups);
        if (count($errors)) {
            $validatorException = new ValidatorException($errors, $entity);

            throw $validatorException;
        }
    }

    /**
     * @return SerializerInterface
     */
    private function getJmsSerializer(): SerializerInterface
    {
        return $this->jmsSerializer;
    }

    /**
     * @return ValidatorInterface
     */
    private function getSymfonyValidator(): ValidatorInterface
    {
        return $this->symfonyValidator;
    }
}