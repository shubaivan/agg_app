<?php

namespace App\QueueModel;

use App\Entity\ManuallyResourceJob;

class ManuallyResourceJobs
{
    /**
     * @var integer
     */
    private $job;

    /**
     * ManuallyResourceJobs constructor.
     * @param int $job
     */
    public function __construct(int $job)
    {
        $this->job = $job;
    }

    /**
     * @return int
     */
    public function getJob(): int
    {
        return $this->job;
    }
}
