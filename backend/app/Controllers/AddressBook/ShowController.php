<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Models\DTOs\AddressBookWithTags;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Repositories\ContactTagRepository;
use AddressBook\Responses\JsonResponse;

class ShowController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;
    private ContactTagRepository $contactTagRepository;

    public function __construct(
        AddressBookRepository $addressBookRepository,
        ContactTagRepository $contactTagRepository
    ) {
        $this->addressBookRepository = $addressBookRepository;
        $this->contactTagRepository = $contactTagRepository;
    }

    public function index(...$urlParams): void
    {
        $id = $urlParams[0];

        $dbEntry = $this->addressBookRepository->find($id);

        if (!$dbEntry) {
            JsonResponse::handle(['message' => 'Address not found'], 404);
        }
        $address = new AddressBookWithTags($dbEntry->toArray());

        $tags = $this->contactTagRepository->getTagsByContactId($id);
        $address->tag_ids = $tags;

        JsonResponse::handle($address->toArray());
    }
}