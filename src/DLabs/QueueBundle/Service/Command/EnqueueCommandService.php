<?php

namespace DLabs\QueueBundle\Service\Command;

use SidekiqJob\Client;
use DLabs\WorkerBundle\Service\Generator\SidekiqWorkerNameGenerator;

class EnqueueCommandService
{
    /** @var Client */
    protected $client;

    /** @var  string */
    protected $queue;

    /** @var  SidekiqWorkerNameGenerator */
    protected $nameGenerator;

    /**
     * @param Client                     $client
     * @param string                     $queue
     * @param SidekiqWorkerNameGenerator $nameGenerator
     */
    public function __construct(Client $client, $queue = 'default', SidekiqWorkerNameGenerator $nameGenerator)
    {
        $this->client   = $client;
        $this->queue    = $queue;
        $this->nameGenerator = $nameGenerator;
    }

    public function execute($handler, $args, $priority = 'md', $retry = true)
    {
        $handler = $this->nameGenerator->generate($handler);
        $this->client->push($handler, $args, $retry, $this->queue);
    }
}