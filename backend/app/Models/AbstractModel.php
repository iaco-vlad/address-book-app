<?php

namespace AddressBook\Models;

abstract class AbstractModel
{
    public function __construct($data = [])
    {
        $this->fill($data);
    }

    public function fill($data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function __get($property)
    {
        if (method_exists($this, 'get' . ucfirst($property))) {
            return $this->{'get' . ucfirst($property)}();
        }
        return $this->$property;
    }

    public function __set($property, $value)
    {
        if (method_exists($this, 'set' . ucfirst($property))) {
            $this->{'set' . ucfirst($property)}($value);
        } else {
            $this->$property = $value;
        }
    }

    protected function convertToArray($value): array
    {
        return $value ? explode(',', $value) : [];
    }
}