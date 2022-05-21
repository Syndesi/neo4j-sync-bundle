![Overview; Doctrine is synchronized in real time to Neo4j.](./docs/assets/Header.png)

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
[![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/Syndesi/neo4j-sync-bundle)](https://codeclimate.com/github/Syndesi/neo4j-sync-bundle/maintainability)

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
