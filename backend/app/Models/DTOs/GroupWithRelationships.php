<?php

namespace AddressBook\Models\DTOs;

use AddressBook\Models\AbstractModel;

class GroupWithRelationships extends AbstractModel
{
    public $id;
    public $name;
    public $direct_contacts;
    public $inherited_contacts;
    public $child_groups;
    public $parent_groups;

    public function getDirectContacts()
    {
        return $this->convertToArray($this->direct_contacts);
    }

    public function getInheritedContacts()
    {
        return $this->convertToArray($this->inherited_contacts);
    }

    public function getChildGroups()
    {
        return $this->convertToArray($this->child_groups);
    }

    public function getParentGroups()
    {
        return $this->convertToArray($this->parent_groups);
    }
}