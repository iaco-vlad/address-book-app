<?php

namespace AddressBook\Repositories;

use AddressBook\Models\City;

class CityRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'cities';
    }

    public function getModelClass(): string
    {
        return City::class;
    }
}