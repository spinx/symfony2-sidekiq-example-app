<?php

namespace DLabs\WorkerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BOF\QueueDomainBundle\Service\Utility\SidekiqWorkerNameGenerator;
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

        $services   = preg_grep($this->nameGenerator->getRegex(), $this->container->getServiceIds());
        $generator  = $this->container->get('dlabs.worker.generator.sidekiq_worker');
        $generator->setSkeletonDirs([
            $this->container->get('kernel')->locateResource('@DLabsWorkerBundle/Resources')
        ]);

        foreach ($services as $serviceName) {
            $class      = $this->nameGenerator->generate($serviceName);
            $filename   = str_replace('__dummy', '', $this->nameGenerator->generateFilename($serviceName));
            $template =  (false === strpos($serviceName, '__dummy')) ? 'template/sidekiq_worker.rb.twig' : 'template/empty_sidekiq_worker.rb.twig';
            $serviceName = str_replace('__dummy', '', $serviceName);

            $generator->generate($template, "app/workers/{$filename}", [
                'namespace' => __NAMESPACE__,
                'service' => $serviceName,
                'class' => $class
            ]);

            $output->writeln(sprintf('Generated <info>%s</info>', "app/workers/{$filename}"));
        }

    }
}