<?php

namespace AddressBook\Repositories;

use AddressBook\Models\ContactGroup;
use PDO;

class ContactGroupRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'contacts_groups';
    }

    public function getModelClass(): string
    {
        return ContactGroup::class;
    }

    public function getByMainGroupIds(array $groupIds): array
    {
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));

        // Query 1: Get groups where main_group_id is in the list
        $query1 = $this->db->prepare("
            SELECT contact_id, main_group_id AS group_id
            FROM {$this->getTable()}
            WHERE main_group_id IN ($placeholders)
            GROUP BY contact_id, group_id, main_group_id
        ");
        $query1->execute($groupIds);
        $result1 = $query1->fetchAll(PDO::FETCH_ASSOC);

        // Query 2: Get groups where group_id is in the list and main_group_id is NULL
        $query2 = $this->db->prepare("
            SELECT contact_id, group_id, main_group_id
            FROM {$this->getTable()}
            WHERE group_id IN ($placeholders)
            GROUP BY contact_id, group_id, main_group_id
        ");
        $query2->execute($groupIds);
        $result2 = $query2->fetchAll(PDO::FETCH_ASSOC);

        // Remove duplicates
        $mergedResult = [];
        foreach (array_merge($result1, $result2) as $entry) {
            $contactId = $entry['contact_id'];
            if (!isset($mergedResult[$contactId])) {
                $mergedResult[$contactId] = $entry;
            } else {
                foreach ($entry as $key => $value) {
                    if (!is_null($value)) {
                        $mergedResult[$contactId][$key] = $value;
                    }
                }
            }
        }

        $modelClass = $this->getModelClass();
        return array_map(function ($row) use ($modelClass) {
            return new $modelClass($row);
        }, $mergedResult);
    }

    public function deleteByGroupId(int $groupId): void
    {
        $query = $this->db->prepare("
            DELETE FROM {$this->getTable()}
            WHERE group_id = ? OR main_group_id = ?
        ");
        $query->execute([$groupId, $groupId]);
    }
}