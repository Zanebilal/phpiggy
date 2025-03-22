<?php

declare(strict_types=1);

namespace Framework;

use ReflectionClass, ReflectionNamedType;
use Framework\Exception\ContainerException; 

class Container{
    private array $definitions = [];
    private array $resolved = [];

    public function addDefinitiones(array $newDefinitions){
        $this->definitions = array_merge($this->definitions, $newDefinitions);
    }

    public function resolve(string $className){
        $reflectionClass = new ReflectionClass($className);

        if(!$reflectionClass->isInstantiable()){
            throw new ContainerException("the class {$className} is not instantiable");
        }

        $constractor = $reflectionClass->getConstructor();
        if(!$constractor){
            return new $className ;
        }

        $params = $constractor->getParameters();
        if(count($params)===0){
            return new $className;
        }

        $dependencies= [];

        foreach($params as $param){
            $name = $param->getName();
            $type = $param->getType();

            if(!$type){
                throw new ContainerException("Failed to resolve class {$className} because param {$name} is missing a type hint");
            }

            if(!$type instanceof ReflectionNamedType || $type->isBuiltin()){
                throw new ContainerException("Failed to resolve class {$className} because invalid param name");
            }

            $dependencies[]= $this->get($type->getName());
        }
        
        return $reflectionClass->newInstanceArgs($dependencies);
    }

    public function get(string $id){
        if(! array_key_exists($id,$this->definitions)){
            throw new ContainerException("Class {$id} does not exist in container.");
        }

        ## check if the dependancy exist in the tble using the class name  
        if(array_key_exists($id,$this->resolved)){
            return $this->resolved[$id];
        }

        ## if the dependacy exist the code bellow never get processed
        $factory = $this->definitions[$id];
        $dependency = $factory($this);

        ## put th dependency key(class nsme) on the table
        $this->resolved[$id] = $dependency;

        return $dependency;
    }
        




}