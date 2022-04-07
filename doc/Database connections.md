# Database connections

This bundle supports multiple Neo4j database connections, e.g. one connection for write- and one for
readonly-statements.

Example configuration:

```yml
# /config/packages/syndesi_neo4j_sync.yaml
syndesi_neo4j_sync:
    connections:
        default:
            default: true
            drivers:
                bolt:
                    url: 'bolt+s://user:password@localhost'
                    default: true
                https:
                    url: 'https://test.com'
                    authentication:
                        user: 'user'
                        password: 'password'
        readonly:
            drivers:
                bolt:
                    url: 'bolt+s://user:password@localhost'
```

See also [Laudis' documentation of the url schemes](https://github.com/neo4j-php/neo4j-php-client#url-schemes) (the
library used for connecting to Neo4j).
