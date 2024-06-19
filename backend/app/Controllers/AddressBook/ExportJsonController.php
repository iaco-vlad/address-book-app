<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Responses\JsonResponse;

class ExportJsonController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;

    public function __construct(AddressBookRepository $addressBookRepository)
    {
        $this->addressBookRepository = $addressBookRepository;
    }

    public function index(...$urlParams): void
    {
        $addresses = $this->addressBookRepository->forExport();

        JsonResponse::file($addresses);
    }
}