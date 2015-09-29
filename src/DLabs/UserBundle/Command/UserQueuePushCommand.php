<?php

namespace DLabs\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;



class UserQueuePushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:queue:push')
            ->setDescription('Push tasks to queeue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numTasks = 100; // add this many

        $container = $this->getContainer();
        $pushService = $container->get('dlabs.worker.push_user');
        $progress = new ProgressBar($output, $numTasks);

        for($i = 0; $i < $numTasks; $i++){
            $pushService->execute('user.do_nothing',[rand()]);
            $progress->advance();
        }
    }
}