<?php

namespace App\Twig;

use JMS\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AppExtension constructor.
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('cast_to_array', [$this, 'castToArray']),
        ];
    }

    public function castToArray($obj)
    {
        $serialize = $this->serializer->serialize($obj, 'json');
        
        return json_decode($serialize, true);
    }
}