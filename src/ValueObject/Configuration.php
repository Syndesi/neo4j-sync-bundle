<?php

declare(strict_types=1);

namespace Syndesi\Neo4jSyncBundle\ValueObject;

use Stringable;
use Syndesi\Neo4jSyncBundle\Contract\IsEqualToInterface;
use Syndesi\Neo4jSyncBundle\Exception\InvalidArgumentException;

class Configuration implements Stringable, IsEqualToInterface
{
    /**
     * @param Client[] $clients
     * @param string   $defaultClient
     * @param int      $pageSize
     * @param bool     $disableDoctrineListeners
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        private readonly array $clients,
        private readonly string $defaultClient,
        private readonly int $pageSize,
        private readonly bool $disableDoctrineListeners
    ) {
        if (empty($this->clients)) {
            throw new InvalidArgumentException("At least one client must be set");
        }
        foreach ($this->clients as $client) {
            if (!($client instanceof Client)) {
                throw new InvalidArgumentException(sprintf("Clients must be of type %s", Client::class));
            }
        }
        $defaultClientFound = false;
        foreach ($this->clients as $name => $client) {
            if ($name === $this->defaultClient) {
                $defaultClientFound = true;
                break;
            }
        }
        if (!$defaultClientFound) {
            throw new InvalidArgumentException("No client with name of default client found");
        }
        if ($this->pageSize <= 1) {
            throw new InvalidArgumentException("Page size must be greater than 1");
        }
    }

    /**
     * @return Client[]
     */
    public function getClients(): array
    {
        return $this->clients;
    }

    public function getDefaultClient(): string
    {
        return $this->defaultClient;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function isDisableDoctrineListeners(): bool
    {
        return $this->disableDoctrineListeners;
    }

    public function __toString()
    {
        $clients = [];
        foreach ($this->clients as $name => $client) {
            if ($name === $this->defaultClient) {
                $clients[] = sprintf("%s (default)", $name);
            } else {
                $clients[] = $name;
            }
        }
        $clients = implode(', ', $clients);

        return sprintf(
            "Configuration with clients (%s), page size of %d and %s doctrine listeners",
            $clients,
            $this->pageSize,
            $this->disableDoctrineListeners ? 'disabled' : 'enabled'
        );
    }

    public function isEqualTo(object $element): bool
    {
        if (!($element instanceof Configuration)) {
            return false;
        }

        $areClientsEqual = true;
        if (count($this->clients) !== count($element->clients)) {
            $areClientsEqual = false;
        } else {
            foreach ($this->clients as $i => $client) {
                if (!$client->isEqualTo($element->clients[$i])) {
                    $areClientsEqual = false;
                    break;
                }
            }
        }

        return
            $areClientsEqual &&
            $this->defaultClient === $element->defaultClient &&
            $this->pageSize === $element->pageSize &&
            $this->disableDoctrineListeners === $element->disableDoctrineListeners;
    }
}
