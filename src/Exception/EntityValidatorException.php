<?php

namespace App\Exception;

interface EntityValidatorException
{
    /**
     * @return string
     */
    public function getIdentity();
}