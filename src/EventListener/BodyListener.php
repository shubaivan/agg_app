<?php


namespace App\EventListener;


use FOS\RestBundle\Decoder\ContainerDecoderProvider;
use FOS\RestBundle\Decoder\JsonDecoder;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BodyListener
{
    /**
     * @var ContainerDecoderProvider
     */
    private $decode;

    /**
     * @var boolean
     */
    private $throwExceptionOnUnsupportedContentType;

    /**
     * @var string
     */
    private $bodyDefaultFormat;

    /**
     * BodyListener constructor.
     * @param ContainerDecoderProvider $decode
     * @param bool $throwExceptionOnUnsupportedContentType
     */
    public function __construct(ContainerDecoderProvider $decode, bool $throwExceptionOnUnsupportedContentType)
    {
        $this->decode = $decode;
        $this->throwExceptionOnUnsupportedContentType = $throwExceptionOnUnsupportedContentType;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        /** @var JsonDecoder $decoder */
        $decoder = $this->getDecode()->getDecoder($this->getBodyDefaultFormat());
        $request = $event->getRequest();
    }

    /**
     * @param string $defaultFormat
     */
    public function setDefaultFormat(string $defaultFormat)
    {
        $this->bodyDefaultFormat = $defaultFormat;
    }

    /**
     * @return ContainerDecoderProvider
     */
    public function getDecode(): ContainerDecoderProvider
    {
        return $this->decode;
    }

    /**
     * @return bool
     */
    public function isThrowExceptionOnUnsupportedContentType(): bool
    {
        return $this->throwExceptionOnUnsupportedContentType;
    }

    /**
     * @return string
     */
    public function getBodyDefaultFormat(): string
    {
        return $this->bodyDefaultFormat;
    }
}