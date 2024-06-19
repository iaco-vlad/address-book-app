<?php

namespace AddressBook\Repositories;

use AddressBook\Models\DTOs\GroupWithRelationships;
use AddressBook\Models\Group;

class GroupRepository extends AbstractRepository
{
    public function getTable(): string
    {
        return 'groups_table';
    }

    public function getModelClass(): string
    {
        return Group::class;
    }

    public function getWithContactsAndGroups($id): mixed
    {
        $query = $this->db->prepare("
            SELECT g.*, 
                GROUP_CONCAT(DISTINCT CASE WHEN cg.main_group_id IS NULL THEN cg.contact_id ELSE NULL END) as direct_contacts,
                GROUP_CONCAT(DISTINCT cg2.id) as child_groups,
                GROUP_CONCAT(DISTINCT pg.id) as parent_groups
            FROM {$this->getTable()} g
            LEFT JOIN contacts_groups cg ON g.id = cg.group_id
            LEFT JOIN groups_groups gg ON g.id = gg.parent_group_id
            LEFT JOIN groups_table cg2 ON gg.child_group_id = cg2.id
            LEFT JOIN groups_groups gg2 ON g.id = gg2.child_group_id
            LEFT JOIN groups_table pg ON gg2.parent_group_id = pg.id
            WHERE g.id = ?
            GROUP BY g.id
            ORDER BY g.id DESC
        ");
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, GroupWithRelationships::class);
        return $query->fetch();
    }

    public function search($searchTerm, $page = 1, $perPage = 10): false|array
    {
        $offset = ($page - 1) * $perPage;

        $query = $this->db->prepare("
            SELECT g.id, g.name, 
                GROUP_CONCAT(DISTINCT CASE WHEN cg.main_group_id IS NULL THEN cg.contact_id ELSE NULL END) as direct_contacts,
                GROUP_CONCAT(DISTINCT CASE WHEN cg.main_group_id IS NOT NULL THEN cg.contact_id ELSE NULL END) as inherited_contacts,
                GROUP_CONCAT(DISTINCT cg2.id) as child_groups,
                GROUP_CONCAT(DISTINCT pg.id) as parent_groups
            FROM {$this->getTable()} g
            LEFT JOIN contacts_groups cg ON g.id = cg.group_id
            LEFT JOIN groups_groups gg ON g.id = gg.parent_group_id
            LEFT JOIN groups_table cg2 ON gg.child_group_id = cg2.id
            LEFT JOIN groups_groups gg2 ON g.id = gg2.child_group_id
            LEFT JOIN groups_table pg ON gg2.parent_group_id = pg.id
            WHERE g.name LIKE ?
            GROUP BY g.id, g.name
            ORDER BY g.id DESC
            LIMIT ?, ?
        ");
        $query->bindValue(1, "%$searchTerm%");
        $query->bindValue(2, $offset, \PDO::PARAM_INT);
        $query->bindValue(3, $perPage, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, GroupWithRelationships::class);
    }
}