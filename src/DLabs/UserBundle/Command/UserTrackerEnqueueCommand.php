<?php

namespace DLabs\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;


class UserTrackerEnqueueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:tracker:enqueue')
            ->setDescription('Push tasks to queeue')
            ->addOption('num', null, InputOption::VALUE_OPTIONAL, 'Number of tasks', 100);
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numTasks = $input->getOption('num'); // add this many

        $container = $this->getContainer();
        $pushService = $container->get('dlabs.worker.enqueue_user');
        $progress = new ProgressBar($output, $numTasks);

        for($i = 0; $i < $numTasks; $i++){
            $pushService->execute('user.tracker_process',[rand()]);
            $progress->advance();
        }
    }
}