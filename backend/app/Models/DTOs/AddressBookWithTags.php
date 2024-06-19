<?php

namespace AddressBook\Models\DTOs;

use AddressBook\Models\AddressBook;

class AddressBookWithTags extends AddressBook
{
    public $tag_ids;
}