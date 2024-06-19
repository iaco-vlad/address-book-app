<?php

namespace AddressBook\Controllers\Groups;

use AddressBook\Controllers\ControllerInterface;
use AddressBook\Models\ContactGroup;
use AddressBook\Models\Group;
use AddressBook\Models\GroupGroup;
use AddressBook\Repositories\ContactGroupRepository;
use AddressBook\Repositories\GroupGroupRepository;
use AddressBook\Repositories\GroupRepository;
use AddressBook\Responses\JsonResponse;

class StoreController implements ControllerInterface
{
    protected GroupRepository $groupRepository;
    protected ContactGroupRepository $contactGroupRepository;
    protected GroupGroupRepository $groupGroupRepository;

    public function __construct(
        GroupRepository $groupRepository,
        ContactGroupRepository $contactGroupRepository,
        GroupGroupRepository $groupGroupRepository
    )
    {
        $this->groupRepository = $groupRepository;
        $this->contactGroupRepository = $contactGroupRepository;
        $this->groupGroupRepository = $groupGroupRepository;
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

        $group = new Group();
        $group->name = $data['name'];
        $group = $this->groupRepository->create($group);

        $this->syncGroupsAndContacts($group->id, $data);

        JsonResponse::handle($group->toArray(), 201);
    }


    protected function syncGroupsAndContacts(int $groupId, mixed $data): void
    {
        $parentGroupIds = $data['parent_group_ids'] ?? [];
        $childGroupIds = $data['child_group_ids'] ?? [];
        $inheritedContactGroups = !empty($parentGroupIds)
            ? $this->contactGroupRepository->getByMainGroupIds($parentGroupIds)
            : [];
        $directContactIds = $this->getDirectContacts($data, $inheritedContactGroups);

        if (!empty($parentGroupIds)) {
            $this->setParentGroups($groupId, $parentGroupIds);
        }
        if (!empty($childGroupIds)) {
            $this->setChildGroups($groupId, $childGroupIds);
        }
        if (!empty($directContactIds)) {
            $this->setDirectContacts($groupId, $directContactIds);
            if (!empty($childGroupIds)) {
                $this->setChildDirectContacts($groupId, $directContactIds, $childGroupIds);
            }
        }
        if (!empty($inheritedContactGroups)) {
            $this->setInheritedContacts($groupId, $inheritedContactGroups);
            if (!empty($childGroupIds)) {
                $this->setChildInheritedContacts($inheritedContactGroups, $childGroupIds);
            }
        }
    }

    /**
     * Sets parent groups for the current group
     *
     * @param int $groupId
     * @param array $parentGroupIds
     * @return void
     */
    private function setParentGroups(int $groupId, array $parentGroupIds): void
    {
        foreach ($parentGroupIds as $parentGroupId) {
            $newGroupGroup = new GroupGroup();
            $newGroupGroup->parent_group_id = $parentGroupId;
            $newGroupGroup->child_group_id = $groupId;
            $this->groupGroupRepository->create($newGroupGroup, true);
        }
    }

    /**
     * Sets all inherited contacts from parent groups to the current group
     *
     * @param int $groupId
     * @param array $inheritedContactGroups
     * @return void
     */
    private function setInheritedContacts(int $groupId, array $inheritedContactGroups): void
    {
        foreach ($inheritedContactGroups as $inheritedContactGroup) {
            $newContactGroup = new ContactGroup();
            $newContactGroup->contact_id = $inheritedContactGroup->contact_id;
            $newContactGroup->group_id = $groupId;
            $newContactGroup->main_group_id = $inheritedContactGroup->group_id;
            $this->contactGroupRepository->create($newContactGroup, true);
        }
    }

    /**
     * Sets inherited contacts from parent groups to all the children groups
     *
     * @param array $inheritedContactGroups
     * @param array $childGroupIds
     * @return void
     */
    private function setChildInheritedContacts(array $inheritedContactGroups, array $childGroupIds): void
    {
        foreach ($inheritedContactGroups as $inheritedContactGroup) {
            foreach ($childGroupIds as $childGroupId) {
                $newContactGroup = new ContactGroup();
                $newContactGroup->contact_id = $inheritedContactGroup->contact_id;
                $newContactGroup->group_id = $childGroupId;
                $newContactGroup->main_group_id = $inheritedContactGroup->group_id;
                $this->contactGroupRepository->create($newContactGroup, true);
            }
        }
    }

    private function setDirectContacts(int $groupId, array $directContactIds): void
    {
        foreach ($directContactIds as $contactId) {
            $newContactGroup = new ContactGroup();
            $newContactGroup->contact_id = $contactId;
            $newContactGroup->group_id = $groupId;
            $newContactGroup->main_group_id = null;
            $this->contactGroupRepository->create($newContactGroup, true);
        }
    }

    /**
     * Sets child groups for the current group
     *
     * @param int $groupId
     * @param array $childGroupIds
     * @return void
     */
    private function setChildGroups(int $groupId, array $childGroupIds): void
    {
        foreach ($childGroupIds as $childGroupId) {
            $newGroupGroup = new GroupGroup();
            $newGroupGroup->parent_group_id = $groupId;
            $newGroupGroup->child_group_id = $childGroupId;
            $this->groupGroupRepository->create($newGroupGroup, true);
        }
    }

    /**
     * Sets all children direct contacts for the current group
     *
     * @param int $groupId
     * @param array $directContactIds
     * @param array $childGroupIds
     * @return void
     */
    private function setChildDirectContacts(int $groupId, array $directContactIds, array $childGroupIds): void
    {
        foreach ($directContactIds as $contactId) {
            foreach ($childGroupIds as $childGroupId) {
                $newContactGroup = new ContactGroup();
                $newContactGroup->contact_id = $contactId;
                $newContactGroup->group_id = $childGroupId;
                $newContactGroup->main_group_id = $groupId;
                $this->contactGroupRepository->create($newContactGroup, true);
            }
        }
    }

    /**
     * Returns direct contacts that are not inherited from parent groups
     *
     * @param mixed $data
     * @param array $inheritedContactGroups
     * @return array
     */
    private function getDirectContacts(mixed $data, array $inheritedContactGroups): array
    {
        $directContactIds = $data['direct_contact_ids'] ?? [];
        return array_filter($directContactIds, function ($id) use ($inheritedContactGroups) {
            foreach ($inheritedContactGroups as $inheritedContactGroup) {
                if ($inheritedContactGroup->contact_id === $id) {
                    return false;
                }
            }
            return true;
        });
    }
}