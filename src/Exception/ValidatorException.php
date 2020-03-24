<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    private $constraintViolatinosList;

    /**
     * @var EntityValidatorException
     */
    private $entity;

    /**
     * ValidatorException constructor.
     * @param ConstraintViolationListInterface $constraintViolatinosList
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     * @param EntityValidatorException $entity
     */
    public function __construct(
        ConstraintViolationListInterface $constraintViolatinosList,
        EntityValidatorException $entity,
        $message = '',
        $code = 400,
        \Exception $previous = null
    )
    {
        $this->entity = $entity;
        $this->constraintViolatinosList = $constraintViolatinosList;
        if ($message === '') {
            $message = $this->getErrorsMessage();
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param null $propertyPath
     * @return array
     */
    public function getErrors($propertyPath = null)
    {
        $violationsList = $this->constraintViolatinosList;
        $output = array();
        foreach ($violationsList as $violation) {
            $output[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        if (null !== $propertyPath) {
            if (array_key_exists($propertyPath, $output)) {
                $output = array($propertyPath => $output[$propertyPath]);
            } else {
                return array();
            }
        }
        return $output;
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function implodeArrayError(array $input)
    {
        $output = implode(', ', array_map(
            function ($v, $k) {
                $reasons = $v;
                if (is_array($v)) {
                    $reasons = implode(',\\n', $v);
                }
                return sprintf("%s='%s'", $k, $reasons);
            },
            $input,
            array_keys($input)
        ));

        return 'Identity:' . $this->getEntity()->getIdentity() . ';' . $output;
    }

    /**
     * @return string
     */
    public function getErrorsMessage()
    {
        $response = $this->getErrors();
        if ($response) {
            return $this->implodeArrayError($response);
        }

        return '';
    }

    /**
     * @return EntityValidatorException
     */
    protected function getEntity(): EntityValidatorException
    {
        return $this->entity;
    }
}