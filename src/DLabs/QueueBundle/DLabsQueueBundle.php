<?php

namespace DLabs\QueueBundle;

use DLabs\QueueBundle\DependencyInjection\Compiler\QueueEventListenerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DLabsQueueBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new QueueEventListenerCompilerPass());
    }
}



