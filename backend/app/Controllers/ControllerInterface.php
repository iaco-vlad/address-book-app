<?php

namespace AddressBook\Controllers;

interface ControllerInterface {
    /**
     * Handle the index action.
     *
     * @param mixed ...$urlParams
     * @return mixed
     */
    public function index(...$urlParams);
}