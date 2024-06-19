<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Responses\JsonResponse;

class DestroyController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;

    public function __construct(AddressBookRepository $addressBookRepository)
    {
        $this->addressBookRepository = $addressBookRepository;
    }

    public function index(...$urlParams): void
    {
        $id = $urlParams[0];

        $address = $this->addressBookRepository->find($id);

        if (!$address) {
            JsonResponse::handle(['message' => 'Address not found'], 404);
        }

        $this->addressBookRepository->delete($id);

        JsonResponse::handle(['message' => 'Address deleted successfully']);
    }
}