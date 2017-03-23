<?php

namespace DLabs\UserBundle\Service\QueueHandler;

use DLabs\WorkerBundle\Service\QueueHandler\QueueHandlerInterface;

class DoNothingQueueHandler implements QueueHandlerInterface
{
    public function execute()
    {
        if (time() % 4 === 0){
            throw new \Exception("That's really unlucky. ");
        }
    }
}
