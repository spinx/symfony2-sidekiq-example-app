<?php

namespace DLabs\WorkerBundle\Service\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;

class SidekiqWorkerGenerator extends Generator
{
    private $filesystem;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Generates twig into $targetPath
     *
     * @param $template
     * @param $targetPath
     * @param $parameters
     */
    public function generate($template, $targetPath, $parameters)
    {
        $this->renderFile($template, $targetPath, $parameters);
    }

}