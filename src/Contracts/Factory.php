<?php

namespace Alnaggar\Mujam\Contracts;

interface Factory
{
    /**
     * Retrieve all resolved store instances.
     *
     * @return array<string, \Alnaggar\Mujam\Contracts\Store> An associative array where keys are store names and values are store instances.
     */
    public function getStores() : array;

    /**
     * Set the store instances.
     * 
     * @param array<string, \Alnaggar\Mujam\Contracts\Store> $stores An associative array where keys are store names and values are store instances.
     * @return static
     */
    public function setStores(array $stores);
}
