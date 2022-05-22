# Home

![Overview; Doctrine is synchronized in real time to Neo4j.](/assets/Header.png)

[![GitHub](https://img.shields.io/github/license/Syndesi/neo4j-sync-bundle)](https://github.com/Syndesi/neo4j-sync-bundle/blob/main/LICENSE)
![Packagist PHP Version Support (specify version)](https://img.shields.io/packagist/php-v/syndesi/neo4j-sync-bundle/dev-refactor)
![Packagist Version](https://img.shields.io/packagist/v/syndesi/neo4j-sync-bundle)
![Packagist Downloads](https://img.shields.io/packagist/dm/syndesi/neo4j-sync-bundle)

[![Unit Tests](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-unit-test.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-unit-test.yml)
[![Mutant Test](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-mutant-test.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-mutant-test.yml)
[![PHPStan](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-phpstan.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-phpstan.yml)
[![Psalm](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-psalm.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-psalm.yml)
[![Code Style](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-code-style.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-code-style.yml)
[![YML lint](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-yml-lint.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-yml-lint.yml)
[![Markdown lint](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-markdown-lint.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-markdown-lint.yml)
[![codecov](https://codecov.io/gh/Syndesi/neo4j-sync-bundle/branch/refactor/graph/badge.svg?token=O6PDLWHO6J)](https://codecov.io/gh/Syndesi/neo4j-sync-bundle)
[![Maintainability](https://api.codeclimate.com/v1/badges/725f510cd334e327ec96/maintainability)](https://codeclimate.com/github/Syndesi/neo4j-sync-bundle/maintainability)

This bundle provides real time synchronization capabilities between
[Doctrine's EntityManager](https://www.doctrine-project.org/) and a [Neo4j database](https://neo4j.com/) in
[Symfony](https://symfony.com/) applications.

## Data model

![Three synchronization types: Independent nodes, independent nodes with dependent relations and independent
relations.](/assets/Synchronization_Types.png)

### Independent Nodes

These are nodes which have a one to one mapping to a Doctrine entity.  
They are required to contain one node label and one identifier. Additional Properties are optional.

### Independent Nodes with Dependent Relations

These are nodes with relations that also have a one to one mapping to a Doctrine entity.  
The generated relations are required to contain one relation label, but an identifier and additional properties are
optional. The relations always start at the node which returned the relationship.

### Independent Relations

These are relations which have a one to one mapping to a Doctrine entity.  
They are required to contain one relation label and one identifier. Additional Properties are optional. Their direction
is defined by their start/child and end/parent nodes. They can both be the same node, therefore creating a loop.

### Indices

[Indices](https://neo4j.com/docs/cypher-manual/current/indexes-for-search-performance/) are non-data-elements which can
tell Neo4j how to optimize lookups. This library does not create them automatically, every index must be explicitly
defined.

## How this bundle works

![Flowchart about how Doctrine lifecycle events are used to create Neo4j statements in real
time](/assets/Flowchart_Doctrine_Events.png)

This bundle works by subscribing to Doctrines `PostPersist`, `PostUpdate` and `PreRemove`
[lifecycle events](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html) and
generating Neo4j statements for changed nodes and relations. These statements are cached by the `Neo4jClient` and
executed during Doctrine's `PostFlush` event.

Every mapped Doctrine entity must be marked by an attribute implementing `NodeAttributeInterface` or
`RelationAttributeInterface`.

## Data persistence

While most logic in this bundle is working hard to keep your data safe, there are a few cases worth looking out for:

- Using the command `neo4j-sync:db:prune` deletes every node and relation from the Neo4j database, whether it was
  managed by this library or not.
- Using the command `neo4j-sync:idnex:prune` deletes every index from the Neo4j database, whether it was managed by this
  library or not.
- Most functions check if a node/relation with the same label and identifier already exists and will merge new data into
  it if possible. Therefore, make sure to use different labels for managed nodes/identifiers if the Neo4j database is
  shared.
- Because data is merged, removed properties will not be removed and will stay in the Neo4j database as long as the
  node/relation itself exists.
- The labels and identifiers of nodes and relations are expected to be static; changing them after the node/relation was
  persisted will most likely fail or result in two nodes/relations, one with the old state and one with the new one.  
  However, changing the label or identifier is possible in manual mode.
