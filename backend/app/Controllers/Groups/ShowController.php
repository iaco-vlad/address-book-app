<?php

namespace AddressBook\Controllers\Groups;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Repositories\GroupRepository;
use AddressBook\Responses\JsonResponse;

class ShowController implements ControllerInterface
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function index(...$urlParams): void
    {
        $id = $urlParams[0];

        $group = $this->groupRepository->getWithContactsAndGroups($id);

        if (!$group) {
            JsonResponse::handle(['message' => 'Group not found'], 404);
        }

        $response = [
            'id' => $group->id,
            'name' => $group->name,
            'direct_contact_ids' => $group->getDirectContacts(),
            'inherited_contact_ids' => $group->getInheritedContacts(),
            'child_group_ids' => $group->getChildGroups(),
            'parent_group_ids' => $group->getParentGroups(),
        ];

        JsonResponse::handle($response);
    }
}