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
     * ValidatorException constructor.
     * @param ConstraintViolationListInterface $constraintViolatinosList
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(
        ConstraintViolationListInterface $constraintViolatinosList,
        $message = '',
        $code = 400,
        \Exception $previous = null
    )
    {
        $this->constraintViolatinosList = $constraintViolatinosList;

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
                return sprintf("%s='%s'", $k, $v);
            },
            $input,
            array_keys($input)
        ));

        return $output;
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
}