<?php

namespace DLabs\QueueBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Mitja Orlic <mitja.orlic@dlabs.si>
 */
class QueueEventListenerCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'kernel.queue_event_listener';
    const PUSH_CS_CONTAINER = 'dlabs.queue.command.enqueue';
    const QUEUE_EVENT_LISTENER_CLASS = 'DLabs\QueueBundle\EventHandler\QueueEventHandler';
    const QUEUE_EVENT_LISTENER_METHOD = 'handle';
    const QUEUE_EVENT_SERVICES_PARAMETERS_KEY = 'dlabs.worker.queue_event_listener.services';

    /** @var  OptionsResolver */
    private $resolver;
    /**
     * Event Tag definition available options
     *
     * @param $tagData
     *
     * @return OptionsResolver
     * @throws \Exception
     */
    private function resolveEventTag($tagData)
    {
        if (!$this->resolver) {
            $this->resolver = (new OptionsResolver())
                ->setDefaults([
                    'method'   => 'handle',
                    'queue'    => 'default',
                    'priority' => 0,
                ])
                ->setRequired([
                    'event',
                ]);
        }

        try {
            return $this->resolver->resolve($tagData);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('QueueEventListener definition error: %s', $e->getMessage()), 0, $e);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds(self::TAG_NAME);
        $pushUSRef = new Reference(self::PUSH_CS_CONTAINER);


        foreach ($services as $serviceName => $tags) {

            foreach ($tags as $tagData) {
                $tagData = $this->resolveEventTag($tagData);

                $lazyEventListener = new Definition(self::QUEUE_EVENT_LISTENER_CLASS, [
                    $pushUSRef,
                    $serviceName,
                    $tagData['method'],
                    $tagData['queue'],
                ]);

                $lazyEventListener
                    ->addTag('kernel.event_listener', [
                        'event'    => $tagData['event'],
                        'method'   => self::QUEUE_EVENT_LISTENER_METHOD,
                        'priority' => $tagData['priority'],
                    ]);

                $lazyEventListenerServiceName = $this->getNewEventListenerServiceName($serviceName, $tagData['event']);

                $container->setDefinition($lazyEventListenerServiceName, $lazyEventListener);
            }
        }

        $container->setParameter(self::QUEUE_EVENT_SERVICES_PARAMETERS_KEY, array_keys($services));
    }

    private function getNewEventListenerServiceName($serviceName, $eventName)
    {
        return sprintf("%s_service",$eventName);
    }
}
