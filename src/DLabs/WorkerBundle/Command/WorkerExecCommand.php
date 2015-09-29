<?php

namespace DLabs\WorkerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class WorkerExecCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('worker:exec')
            ->setHelp(<<<EOF
This command should be called like this: 
worker:exec dlabs.user.queue_handler.do_nothing'[1, true, "test"]'

[..]  = arguments passed to handler

EOF
            )
            ->setDescription('Executes a worker with arguments')
            ->addArgument('worker', InputArgument::REQUIRED, 'Queue handler service name', null)
            ->addArgument('payload', InputArgument::OPTIONAL, 'Job payload', null)
            ->addOption('decode', null, InputOption::VALUE_NONE, 'Payload is base64 encoded and gzipped, decode it');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $workerName = $input->getArgument('worker');
        $rawPayload = ($input->getArgument('payload') === null) ? '[]' : $input->getArgument('payload');
        $payload = ($input->getOption('decode')) ? json_decode(gzdecode(base64_decode($rawPayload))) : json_decode($rawPayload);

        $workerService = $this->getContainer()->get($workerName);
        call_user_func_array(array($workerService, 'execute'), $payload);
    }
}