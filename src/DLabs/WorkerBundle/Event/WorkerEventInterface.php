<?php

/**
 * WorkerEventInterface
 */
interface WorkerEventInterface
{
    /**
     * Gets the ID of object carried.
     *
     * @return int|string
     */
    public function getObjectId();

    /**
     * Gets event data.
     *
     * @return array
     */
    public function getData();

    /**
     * Gets a property of the data.
     *
     * @param mixed $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);
}