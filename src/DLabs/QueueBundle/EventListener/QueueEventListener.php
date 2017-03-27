<?php

namespace DLabs\QueueBundle\EventListener;

use DLabs\QueueBundle\Service\Command\EnqueueCommandService;
use DLabs\WorkerBundle\Service\Generator\SidekiqWorkerNameGenerator;


/**
 * @author  Mitja Orlic <mitja.orlic@dlabs.si>
 */
class QueueEventListener
{
    /** @var EnqueueCommandService */
    private $enqueueCS;
    /** @var string */
    private $eventListenerService;
    /** @var string */
    private $queueName;


    /**
     * QueueEventListener constructor.
     *
     * @param EnqueueCommandService $enqueueCS
     * @param string                $eventListenerService
     * @param string                $method
     * @param string                $queueName
     */
    public function __construct(
        EnqueueCommandService $enqueueCS,
        SidekiqWorkerNameGenerator $nameGenerator,
        $eventListenerService,
        $method,
        $queueName = 'default'
    ) {
        $this->enqueueCS            = $enqueueCS;
        $this->nameGenerator       = $nameGenerator;
        $this->eventListenerService = $eventListenerService;
        $this->queueName            = $queueName;
        $this->method               = $method;
    }

    /**
     * @param ObjectEvent $event
     * @param             $eventName
     */
    public function handle(ObjectEvent $event)
    {
        $this->enqueueCS->execute(
            $this->nameGenerator->generateFromEventHandler($this->eventListenerService),
            $event->getData()
        );
    }
}
