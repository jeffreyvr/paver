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

    public function handle()
    {
        $class = $this->get('class');
        $state = $this->get('state', []);
        $call = $this->get('call');
        $args = $this->get('args');

        $reflectionClass = new ReflectionClass($class);

        if (! $this->checkIfAllowedClass($reflectionClass)) {
            $this->json(['message' => 'Not allowed'], 401);
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
