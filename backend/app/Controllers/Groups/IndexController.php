<?php

namespace AddressBook\Controllers\Groups;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Models\DTOs\GroupWithRelationships;
use AddressBook\Repositories\GroupRepository;
use AddressBook\Responses\JsonResponse;

class IndexController implements ControllerInterface
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function index(...$urlParams): void
    {
        $searchTerm = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 10;

        $groups = $this->groupRepository->search($searchTerm, $page, $perPage);

        $data = [];
        foreach ($groups as $group) {
            /** @var GroupWithRelationships $group */
            $data[] = [
                'id' => $group->id,
                'name' => $group->name,
                'direct_contact_ids' => $group->getDirectContacts(),
                'inherited_contact_ids' => $group->getInheritedContacts(),
                'child_group_ids' => $group->getChildGroups(),
                'parent_group_ids' => $group->getParentGroups(),
            ];
        }

        $response = [
            'data' => $data,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $this->groupRepository->count(),
            ],
        ];

        JsonResponse::handle($response);
    }
}