<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Symfony\Component\DependencyInjection\Parameter;
use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;

class Client implements Stringable, IsEqualToInterface
{
    /**
     * @param Driver[] $drivers
     * @param string   $defaultDriver
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly array $drivers,
        private readonly string $defaultDriver
    ) {
        if (empty($this->drivers)) {
            throw new InvalidArgumentException("At least one driver must be set");
        }
        if (count(array_unique(array_keys($this->drivers))) !== count(array_keys($this->drivers))) {
            throw new InvalidArgumentException("All driver names must be unique");
        }
        foreach ($this->drivers as $driver) {
            if (!($driver instanceof Driver)) {
                throw new InvalidArgumentException(sprintf("Drivers must be of type %s", Driver::class));
            }
        }
        $defaultDriverFound = false;
        foreach ($this->drivers as $name => $driver) {
            if ($name === $this->defaultDriver) {
                $defaultDriverFound = true;
                break;
            }
        }
        if (!$defaultDriverFound) {
            throw new InvalidArgumentException(sprintf("No driver with name of default driver (%s) found", $this->defaultDriver));
        }
    }

    /**
     * @return Driver[]
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    public function getDefaultDriver(): string
    {
        return $this->defaultDriver;
    }

    public function __toString()
    {
        $drivers = [];
        foreach ($this->drivers as $name => $driver) {
            if ($this->defaultDriver === $name) {
                $drivers[] = sprintf("%s of type %s (default)", $name, $driver->getType()->value);
            } else {
                $drivers[] = sprintf("%s of type %s", $name, $driver->getType()->value);
            }
        }
        $drivers = implode(', ', $drivers);

        return sprintf("client with drivers (%s)", $drivers);
    }

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof Client)) {
            return false;
        }

        $areDriversEqual = true;
        if (count($this->drivers) !== count($element->drivers)) {
            $areDriversEqual = false;
        } else {
            foreach ($this->drivers as $i => $driver) {
                if (!$driver->isEqualTo($element->drivers[$i])) {
                    $areDriversEqual = false;
                    break;
                }
            }
        }

        return
            $areDriversEqual &&
            $this->defaultDriver === $element->defaultDriver;
    }
}
