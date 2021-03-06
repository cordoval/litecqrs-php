<?php

namespace LiteCQRS\Plugin\Silex;

use Silex\Application;
use LiteCQRS\Bus\EventName;
use LiteCQRS\Bus\AbstractEventMessageBus;

/**
 * @package LiteCQRS
 */
class ApplicationEventBus extends AbstractEventMessageBus
{
    protected $application;
    protected $eventServices = array();

    /**
     * @param Application $application
     * @param array $proxyFactories
     */
    public function __construct(Application $application, array $proxyFactories)
    {
        $this->application = $application;

        parent::__construct($proxyFactories);
    }

    /**
     * @param array $eventServices
     */
    public function registerServices(array $eventServices)
    {
        $this->eventServices = array_change_key_case($eventServices);
    }

    /**
     * @param string $eventName
     * @return array
     */
    protected function getHandlers(EventName $eventName)
    {
        $handlers = array();
        $eventName = strtolower($eventName);

        if (!isset($this->eventServices[$eventName])) {
            return array();
        }

        foreach ((array) $this->eventServices[$eventName] as $serviceIds) {
            foreach ((array) $serviceIds as $serviceId) {
                $handlers[] = $this->application[$serviceId];
            }
        }

        return $handlers;
    }
}
