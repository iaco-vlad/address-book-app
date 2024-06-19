<?php

namespace AddressBook\Repositories;

use AddressBook\Models\GroupGroup;
use PDO;

class GroupGroupRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'groups_groups';
    }

    public function getModelClass(): string
    {
        return GroupGroup::class;
    }

    public function deleteByGroupId(int $groupId): void
    {
        $query = $this->db->prepare("
            DELETE FROM {$this->getTable()} 
            WHERE parent_group_id = ? OR child_group_id = ?
        ");
        $query->execute([$groupId, $groupId]);
    }
}