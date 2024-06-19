<?php

namespace AddressBook\Controllers\AddressBook;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\AddressBookRepository;
use AddressBook\Responses\JsonResponse;

class IndexController implements ControllerInterface
{
    private AddressBookRepository $addressBookRepository;

    public function __construct(AddressBookRepository $addressBookRepository)
    {
        $this->addressBookRepository = $addressBookRepository;
    }

    public function index(...$urlParams): void
    {
        $searchTerm = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 10;
        $tagId = $_GET['tag'] ?? null;

        $response = [
            'data' => $this->addressBookRepository->search($searchTerm, $page, $perPage, $tagId),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $this->addressBookRepository->count($searchTerm, $tagId),
            ],
        ];

        JsonResponse::handle($response);
    }
}