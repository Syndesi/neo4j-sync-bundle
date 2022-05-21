![Overview; Doctrine is synchronized in real time to Neo4j.](./docs/assets/Header.png)

[![Unit Tests](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-unit-test.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-unit-test.yml)
[![Mutant Test](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-mutant-test.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-mutant-test.yml)
[![PHPStan](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-phpstan.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-phpstan.yml)
[![Psalm](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-psalm.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-psalm.yml)
[![Code Style](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-code-style.yml/badge.svg)](https://github.com/Syndesi/neo4j-sync-bundle/actions/workflows/ci-code-style.yml)
[![codecov](https://codecov.io/gh/Syndesi/neo4j-sync-bundle/branch/refactor/graph/badge.svg?token=O6PDLWHO6J)](https://codecov.io/gh/Syndesi/neo4j-sync-bundle)

# Syndesi's Neo4jSyncBundle

This bundle provides real time synchronization capabilities between Doctrine's EntityManager and a Neo4j database.

Links:

- [Documentation](https://syndesi.github.io/neo4j-sync-bundle)
- [Laudi's Neo4j Client](https://github.com/neo4j-php/neo4j-php-client) (no affiliation, but is a core dependency of
  this library)

## Development

```bash
# yml linter, empty result if no errors are found
docker run --rm -it -v $(pwd):/data cytopia/yamllint .
# markdown linter
docker run --rm -v $(pwd):/work tmknom/markdownlint '**/*.md' --ignore vendor
```
