<?php

namespace Ophp\Subway;

/**
 * Keeps a queue of running processes
 */
class Queue
{
    /**
     * The list of processes
     * Must always be numerically and senquentially indexed
     * @var array
     */
    protected $processes = [];
    
    /**
     *
     * @var int
     */
    protected $currentProcess;

    public function addProcess($process)
    {
        $this->processes[] = $process;
    }

    public function getNextProcess()
    {
        if (count($this->processes) > 0) {
            if (!isset($this->currentProcess)) {
                $this->currentProcess = 0;
            } else {
                $this->currentProcess++;
                if (!isset($this->processes[$this->currentProcess])) {
                    $this->currentProcess = 0;
                }
            }
            $process = $this->processes[$this->currentProcess];
        } else {
            $process = null;
        }
        return $process;
    }

    public function removeProcess($index)
    {
        if (isset($this->processes[$index])) {
            unset($this->processes[$index]);
            $this->processes = array_values($this->processes);
        }
    }
    
    public function execute()
    {
        $process = $this->getNextProcess();
        while (isset($process)) {
            if ($process()) {
                $this->removeProcess($this->currentProcess);
            }
            $process = $this->getNextProcess();
        }
    }

}
