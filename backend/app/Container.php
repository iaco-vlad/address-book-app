<?php

namespace AddressBook;

use Exception;
use PDO;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class Container
{
    private function dependencies(): array
    {
        return [
            PDO::class => new PDO(
                'mysql:host=mysql;dbname=' . $_ENV['MYSQL_DATABASE'],
                $_ENV['MYSQL_USER'],
                $_ENV['MYSQL_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            ),
        ];
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function resolve($class)
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $parameterName = $parameter->getName();
            $type = $parameter->getType();
            if ($type === null) {
                throw new Exception("The dependency '{$parameterName}' is null for class '{$class}(" . implode(',', $parameters) . ")");
            }

            $parameterClass = $type->getName();

            if (isset($this->dependencies()[$parameterClass])) {
                $dependencies[] = $this->dependencies()[$parameterClass];
            } elseif ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->resolve($parameterClass);
            } else {
                throw new Exception("Cannot resolve dependency '{$parameterName}' for class '{$class}(" . implode(',', $parameters) . ")");
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}