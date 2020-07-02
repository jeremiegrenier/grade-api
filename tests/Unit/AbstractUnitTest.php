<?php

declare(strict_types=1);

/**
 * This file is part of grade-api project
 */

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class AbstractUnitTest.
 *
 * @author jgrenier
 *
 * @version 1.0.0
 */
abstract class AbstractUnitTest extends TestCase
{
    /**
     * Call protected method of an object.
     *
     * @param mixed $object
     *
     * @return mixed
     */
    protected function callProtectedMethod($object, string $methodName, array $parameters)
    {
        $reflection = new ReflectionClass($object);
        $reflection_method = $reflection->getMethod($methodName);
        $reflection_method->setAccessible(true);

        return $reflection_method->invokeArgs($object, $parameters);
    }

    /**
     * Get value of a protected property.
     *
     * @param mixed $object
     *
     * @return mixed
     */
    protected function getProtectedProperty($object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($propertyName);
        $reflection_property->setAccessible(true);

        return $reflection_property->getValue($object);
    }
}
