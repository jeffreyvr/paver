<?php

namespace Jeffreyvr\Paver\Endpoints;

use Jeffreyvr\Paver\Blocks\Block;
use Jeffreyvr\Paver\Blocks\Options\Option;
use ReflectionClass;

class Resolve extends Endpoint
{
    protected function checkIfAllowedClass($class)
    {
        $acceptedClasses = [Option::class, Block::class];

        foreach ($acceptedClasses as $acceptedClass) {
            if ($class->isSubclassOf($acceptedClass)) {
                return true;
            }
        }

        return false;
    }

    protected function checkIfAllowedMethod(\ReflectionClass $class, string $method): bool
    {
        if (! $class->hasMethod($method)) {
            return false;
        }

        $reflectionMethod = $class->getMethod($method);

        return $reflectionMethod->isPublic() && ! $reflectionMethod->isStatic();
    }

    public function handle()
    {
        $class = $this->get('class');
        $state = $this->get('state', []);
        $call = $this->get('call');
        $args = $this->get('args');

        if (! class_exists($class)) {
            $this->json(['message' => 'Class not found'], 404);
        }

        $reflectionClass = new ReflectionClass($class);

        if (! $this->checkIfAllowedClass($reflectionClass)) {
            $this->json(['message' => 'Class not allowed'], 403);
        }

        if (! $this->checkIfAllowedMethod($reflectionClass, $call)) {
            $this->json(['message' => 'Method not allowed'], 403);
        }

        $instance = $reflectionClass->newInstanceArgs($state);

        $constructor = $reflectionClass->getConstructor();

        if ($constructor !== null) {
            $constructorParams = [];

            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                if (isset($state[$paramName])) {
                    $constructorParams[] = $state[$paramName];
                } else {
                    $constructorParams[] = $param->getDefaultValue();
                }
            }
        } else {
            $instance = new $class;
        }

        foreach ($state as $key => $value) {
            $instance->{$key} = $value;
        }

        $method = $reflectionClass->getMethod($call);
        $result = $method->invokeArgs($instance, $args);

        $this->json([
            'result' => $result,
        ]);
    }
}
