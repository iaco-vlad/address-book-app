<?php

namespace AddressBook\Models;

class AddressBook extends AbstractModel
{
    public $id;
    public $last_name;
    public $first_name;
    public $email;
    public $street;
    public $zip_code;
    public $city_id;
}