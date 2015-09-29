<?php

namespace DLabs\UserBundle\Service\QueueHandler;

use DLabs\WorkerBundle\Service\QueueHandler\QueueHandlerInterface;

class DoNothingQueueHandler implements QueueHandlerInterface
{
    public function execute()
    {
        // Does nothing
    }
}
