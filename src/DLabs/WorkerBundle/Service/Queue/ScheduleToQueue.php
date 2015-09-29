<?php

namespace DLabs\WorkerBundle\Service\Queue;

use SidekiqJob\Client;
use DLabs\WorkerBundle\Service\Generator\SidekiqWorkerNameGenerator;

/**
 */
class ScheduleToQueue
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

    /**
     * @param \Datetime|int $at
     * @param string        $handler
     * @param array         $args
     * @param bool          $retry
     */
    public function execute($at, $handler, $args = [], $retry=true)
    {
        $handler = $this->nameGenerator->generate($handler);

        // need to pass microtime(true) format
        $at = ($at instanceof \DateTime) ? $at->getTimestamp().'.000' : $at.'.000';

        $this->client->schedule($at, $handler, $args, $retry, $this->queue);
    }

}