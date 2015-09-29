<?php

namespace DLabs\WorkerBundle\Service\QueueHandler;

interface QueueHandlerInterface
{
    public function execute();
}
