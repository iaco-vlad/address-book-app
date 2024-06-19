<?php

namespace AddressBook\Controllers\Cities;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\CityRepository;
use AddressBook\Responses\JsonResponse;

class IndexController implements ControllerInterface
{
    private CityRepository $citiesRepository;

    public function __construct(CityRepository $citiesRepository)
    {
        $this->citiesRepository = $citiesRepository;
    }

    public function index(...$urlParams): void
    {
        $cities = $this->citiesRepository->all();

        JsonResponse::handle($cities);
    }
}