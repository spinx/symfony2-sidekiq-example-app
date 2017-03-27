<?php

namespace DLabs\WorkerBundle\Service\Generator;

/**
 * Generates Sidekiq class name
 * Class SidekiqWorkerNameGenerator
 */
class SidekiqWorkerNameGenerator
{
    /** @var string */
    protected $queueListenerTag = 'kernel.queue_event_listener';

    /** @var string */
    private $regex = "/dlabs\.([a-z]{1,})\.queue_handler\.()([a-z_]{1,})/";

    /** @var string */
    private $regexHandler = "/dlabs\.([a-z]{1,})\.event\.()([a-z_]{1,})\.event_handler/";
    /**
     * Given SF2 queue_handler service name, returns Sidekiq class name
     *
     * @param $serviceName
     * @return string
     */
    public function generate($serviceName)
    {
        $serviceName = $this->serviceFromShorthand($serviceName);
        $parts = $this->getParts($serviceName);
        $className = implode('', array_map('ucfirst', array_map(function($val){
            return implode('', array_map('ucfirst', explode('_', $val)));
        }, $parts)));

        return $className;
    }

    /**
     * Given SF2 service name, returns Sidekiq worker filename
     *
     * @param $serviceName
     * @return string
     */
    public function generateFilename($serviceName)
    {

        $parts = $this->getParts($serviceName);
        return sprintf('%s.rb', implode('_', $parts));
    }

    /**
     * Given SF2 queue_handler service name, returns Sidekiq worker filename
     *
     * @param $serviceName
     * @return string
     */
    public function generateFilenameFromEventHandler($serviceName)
    {
        $parts = $this->getPartsForEventHandler($serviceName);
        return sprintf('%s.rb', implode('_', array_merge($parts, ['event'])));
    }

    /**
     * Given shorthand name, returns fully qualified SF2 service name
     * @param $serviceName
     * @return string
     */
    public function generateFromEventHandler($serviceName)
    {
        $parts = $this->getPartsForEventHandler($serviceName);
        $className = implode('', array_map('ucfirst', array_map(function($val){
            return implode('', array_map('ucfirst', explode('_', $val)));
        }, array_merge($parts, ['Event']))));

        return $className;
    }

    /**
     * Given shorthand name, returns fully qualified SF2 service name
     * @param $serviceName
     * @return string
     */
    public function serviceFromShorthand($serviceName)
    {
        if(strpos($serviceName, 'dlabs.') !== 0){
            $parts = explode('.', $serviceName);
            $serviceName = sprintf('dlabs.%s.queue_handler.%s', array_shift($parts), implode('.', $parts));
        }
        return $serviceName;
    }
    
    /**
     * Returns queue_handler service definition regex
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Returns queue_listener service tag
     * @return string
     */
    public function getTag()
    {
        return $this->queueListenerTag;
    }

    /**
     * Returns service name parts
     *
     * @param $serviceName
     * @return array|\Exception
     */
    private function getParts($serviceName)
    {
        $serviceName = $this->serviceFromShorthand($serviceName);
        preg_match($this->regex, $serviceName, $matches);
        array_shift($matches);
        if(empty($matches)){
            throw new \Exception(sprintf('Incorrectly named queue handler service. Expected: dlabs.*.queue_handler.* got %s',$serviceName));
        }
        return array_filter($matches, 'strlen');
    }

    /**
     * Returns service name parts
     *
     * @param $serviceName
     * @return array|\Exception
     */
    private function getPartsForEventHandler($serviceName)
    {
        preg_match($this->regexHandler, $serviceName, $matches);
        array_shift($matches);
        if(empty($matches)){
            throw new \Exception(sprintf('Incorrectly named queue handler service. Expected: dlabs.*.event.*.event_handler got %s',$serviceName));
        }
        return array_filter($matches, 'strlen');
    }

    /**
     * Given Sidekiq worker class name returns SF2 service name
     *
     * @param $class
     * @return string
     */
    public function serviceFromClass($class)
    {
        $chunks = array_map('strtolower', array_filter(preg_split('/(?=[A-Z])/', $class), 'strlen'));
        $serviceName = sprintf("dlabs.%s.queue_handler.%s", array_shift($chunks), implode('_', $chunks));

        return $serviceName;
    }

}