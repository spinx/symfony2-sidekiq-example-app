<?php

namespace DLabs\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DomainEvent extends Event
{
    /** @var bool */
    private $isLocked = false;
    /** @var int */
    private $objectId;
    /** @var array */
    private $data;
    /** @var int */
    private $userId;
    /** @var array */
    private $requestData = [];
    /** @var string */
    private $eventName;
    /** @var string Useful for setting specific date onto event (When the event happened) */
    private $dateTime;

    /**
     * @param int        $objectId   Every event must have an object Id
     * @param array      $data       The data related to that object
     * @param string     $eventName
     *
     * @throws \Exception
     */
    public function __construct($objectId, array $data = [], $eventName = null)
    {
        $this->objectId   = $objectId;
        $this->data       = $data;
        $this->eventName = $eventName;

        $this->setDateTime();
    }

    /**
     * @param mixed $dateTime
     *
     * @return $this
     */
    public function setDateTime($dateTime = null)
    {
        $this->checkLock();

        $this->dateTime = $dateTime ?: date('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return string
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return mixed
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int $objectId
     *
     * @return $this
     */
    public function setObjectId($objectId)
    {
        $this->checkLock();

        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->checkLock();

        $this->data = $data;

        return $this;
    }

    /**
     * Gets a property of the data array
     *
     * @param      $key
     *
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Once this method has been called, the event contents cannot be changed!
     * (Immutable events)
     */
    public function lock()
    {
        if (!$this->getEventName() || !$this->getObjectId()) {
            throw new \Exception('Cannot dispatch an event without an object id and event name');
        }

        $this->isLocked = true;

        return $this;
    }

    /**
     * Internal helper that will prevent ObjectEvent contents to be changed after the event is dispatched
     *
     * @throws \Exception
     */
    private function checkLock()
    {
        if ($this->isLocked) {
            throw new \Exception('You cannot change the contents of an event that has been dispatched!');
        }
    }
}