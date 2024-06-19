<?php

namespace AddressBook\Controllers\Groups;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\ContactGroupRepository;
use AddressBook\Repositories\GroupGroupRepository;
use AddressBook\Repositories\GroupRepository;
use AddressBook\Responses\JsonResponse;

class UpdateController extends StoreController implements ControllerInterface
{

    public function __construct(
        GroupRepository $groupRepository,
        ContactGroupRepository $contactGroupRepository,
        GroupGroupRepository $groupGroupRepository
    )
    {
        parent::__construct($groupRepository, $contactGroupRepository, $groupGroupRepository);
    }

    public function index(...$urlParams): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data === null) {
            JsonResponse::handle(['message' => 'Invalid JSON'], 400);
        }

        if (!isset($data['name'])) {
            JsonResponse::handle(['message' => "Missing required field: name"], 400);
        }

        $id = $urlParams[0];

        $group = $this->groupRepository->find($id);

        if (!$group) {
            JsonResponse::handle(['message' => 'Group not found'], 404);
        }

        $group->name = $data['name'];
        $this->groupRepository->update($id, $group);

        $this->removeRelatedEntries($id);

        $this->syncGroupsAndContacts($id, $data);

        JsonResponse::handle($group->toArray(), 201);
    }

    private function removeRelatedEntries(int $groupId): void
    {
        $this->contactGroupRepository->deleteByGroupId($groupId);

        $this->groupGroupRepository->deleteByGroupId($groupId);
    }
}