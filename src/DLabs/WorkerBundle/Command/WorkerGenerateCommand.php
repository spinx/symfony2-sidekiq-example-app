<?php

namespace DLabs\WorkerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Generates Sidekiq worker files
 *
 * Class WorkerSidekiqGenerateCommand
 * @package BOF\QueueDomainBundle\Command
 */
class WorkerGenerateCommand extends ContainerAwareCommand
{
    /** @var ContainerInterface  */
    protected $container;

    /** @var SidekiqWorkerNameGenerator  */
    protected $nameGenerator;

    protected function configure()
    {
        $this
            ->setName('worker:generate')
            ->setDescription('Generate Sidekiq Ruby classes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();
        $this->nameGenerator = $this->container->get('dlabs.worker.generator.sidekiq_worker_name');
        $this->generator = $this->container->get('dlabs.worker.generator.sidekiq_worker');
        $this->nameGenerator = $this->container->get('dlabs.worker.generator.sidekiq_worker_name');
        $this->generator->setSkeletonDirs([$this->container->get('kernel')->locateResource('@DLabsWorkerBundle/Resources')]);

        $this->generateQueueListenerClasses($output);
        $this->generateNamedJobClasses($output);

    }

    /**
     * @param OutputInterface $output
     * @param                 $serviceName
     * @param                 $filename
     * @param                 $class
     */
    private function createFile(OutputInterface $output, $serviceName, $filename, $class)
    {
        if (false === strpos($serviceName, '__dummy')) {
            $this->generator->generate(
                'template/sidekiq_worker.rb.twig',
                "app/workers/{$filename}",
                [
                    'namespace' => __NAMESPACE__,
                    'service'   => $serviceName,
                    'class'     => $class
                ]
            );

            $output->writeln(sprintf('Generated <info>%s</info>', "app/workers/{$filename}"));
        } else {
            $filename = str_replace('__dummy', '', $filename);
            $output->writeln(sprintf('Skipping <info>%s</info>', "app/workers/{$filename}"));
        }
    }

    private function generateQueueListenerClasses(OutputInterface $output)
    {
        $services = $this->container->getParameter('dlabs.worker.queue_event_listener.services');

        foreach ($services as $serviceName) {
            $class = $this->nameGenerator->generateFromEventHandler($serviceName);
            $filename = $this->nameGenerator->generateFilenameFromEventHandler($serviceName);
            $this->createFile($output, $serviceName, $filename, $class);
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function generateNamedJobClasses(OutputInterface $output)
    {
        $services = preg_grep($this->nameGenerator->getRegex(), $this->container->getServiceIds());

        foreach ($services as $serviceName) {
            $class = $this->nameGenerator->generate($serviceName);
            $filename = $this->nameGenerator->generateFilename($serviceName);
            $this->createFile($output, $serviceName, $filename, $class);
        }
    }

}