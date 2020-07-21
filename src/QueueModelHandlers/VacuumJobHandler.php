<?php

namespace App\QueueModelHandlers;

use App\Entity\Product;
use App\Exception\ValidatorException;
use App\QueueModel\AdrecordDataRow;
use App\QueueModel\VacuumJob;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VacuumJobHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * VacuumJobHandler constructor.
     * @param EntityManagerInterface $em,
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AdrecordDataRow $adrecordDataRow
     * @throws ValidatorException
     * @throws \Throwable
     */
    public function __invoke(VacuumJob $job)
    {
        if ($job->getRun()) {
            $this->em->getRepository(Product::class)->autoVACUUM();
        }
    }
}
