<?php


namespace App\QueueModel;

class VacuumJob
{
    /**
     * @var bool
     */
    private $run = false;

    /**
     * VacuumJob constructor.
     * @param bool $run
     */
    public function __construct(bool $run)
    {
        $this->run = $run;
    }

    /**
     * @return bool
     */
    public function getRun(): bool
    {
        return $this->run;
    }
}