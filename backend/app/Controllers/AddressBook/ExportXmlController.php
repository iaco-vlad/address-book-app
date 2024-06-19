<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Responses\XmlResponse;

class ExportXmlController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;

    public function __construct(AddressBookRepository $addressBookRepository)
    {
        $this->addressBookRepository = $addressBookRepository;
    }

    public function index(...$urlParams): void
    {
        $addresses = $this->addressBookRepository->forExport();

        XmlResponse::file($addresses, 'addresses');
    }
}