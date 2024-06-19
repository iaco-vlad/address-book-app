<?php

namespace AddressBook\Controllers\Groups;


use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\GroupRepository;
use AddressBook\Responses\JsonResponse;

class DestroyController implements ControllerInterface
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function index(...$urlParams): void
    {
        $id = $urlParams[0];

        $group = $this->groupRepository->find($id);

        if (!$group) {
            JsonResponse::handle(['message' => 'Group not found'], 404);
        }

        $this->groupRepository->delete($id);

        JsonResponse::handle(['message' => 'Group deleted']);
    }
}