<?php

namespace DLabs\WorkerBundle\Command;

use BOF\UtilityBundle\Command\Base\BaseCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class WorkerProcessQueueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('worker:queue:process')
            ->setDescription('Processes queue instantly')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $redisClient = $container->get('dlabs.worker.sidekiq_client.default')->getRedis();
        $generator = $container->get('dlabs.worker.generator.sidekiq_worker_name');
        $queues = $redisClient->smembers('queues');

        foreach ($queues as $queue) {
            $queueName = 'queue:'.$queue;
            $len = $redisClient->llen($queueName);

            if($len > 0){
                $output->writeln(sprintf("Processing queue <info>%s</info>", $queue));
                $progress = new ProgressBar($output, $len);

                for($i=0; $i < $len; $i++){
                    $task = json_decode($redisClient->lpop($queueName), true);
                    $output->write(sprintf(" Job <comment>%s</comment>", $task['jid']));
                    $workerService = $container->get($generator->serviceFromClass($task['class']));

                    call_user_func_array(array($workerService, 'execute'), $task['args']);

                    $progress->advance();
                }
                $progress->finish();
            }
        }
    }
}