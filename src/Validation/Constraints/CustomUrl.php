<?php


namespace App\Validation\Constraints;


use Symfony\Component\Validator\Constraints\Url;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class CustomUrl extends Url
{

}