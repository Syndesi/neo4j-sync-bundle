# Commands

All commands use the default Neo4j client and its default driver.

## Database Purge

Command for purging/deleting the Neo4j database. Indices are not affected.

### Example

```bash
php bin/console neo4j-sync:db:purge
```

```txt
Purge all nodes and relations? (yes/NO): y

Purging all nodes and relations...

                                                                                                                        
 [OK] Database purge completed after 00:00:06 (H:m:s)                                                                   
                                                                                                                        

To purge indices please run command "neo4j-sync:index:purge"
```

## Database Sync

Command for synchronizing the Neo4j database.

### Example

```bash
php bin/console neo4j-sync:db:sync --memory=512
```

```text
Memory set to 512 MB
Loading data providers... done

The following providers were found:
+-------------------------------------------------------------------+-------------------------+-------+
| Provider                                                          | Content                 | Count |
+-------------------------------------------------------------------+-------------------------+-------+
| Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider         | App\Entity\SimpleNode   | 10000 |
| Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider         | App\Entity\ChildNode    | 37897 |
| Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider         | App\Entity\ParentNode   | 10000 |
| Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeRelationProvider | App\Entity\ChildNode    | 37897 |
| Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncRelationProvider     | App\Entity\DemoRelation | 0     |
+-------------------------------------------------------------------+-------------------------+-------+

Sync type: MERGE

Synchronize Neo4j database? (yes/NO): y

Synchronizing database...
Synchronized Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider with App\Entity\SimpleNode
Synchronized Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider with App\Entity\ChildNode
Synchronized Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeProvider with App\Entity\ParentNode
Synchronized Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncNodeRelationProvider with App\Entity\ChildNode
Synchronized Syndesi\Neo4jSyncBundle\Provider\DatabaseSyncRelationProvider with App\Entity\DemoRelation

                                                                                                                        
 [OK] Synchronization completed after 00:01:58 (H:m:s)                                                                  
                                                                                                                        
```

## Index List

Command for listing all indices, both defined and indices defined in the Neo4j database.

### Example

```bash
php bin/console neo4j-sync:index:list
```

```text
+----------------+-------------------+-------+------------+-----------------+--------------------+----------+
| Name           | On                | Type  | Properties | Defined in Code | Exists in Database | Conflict |
+----------------+-------------------+-------+------------+-----------------+--------------------+----------+
| child_node_id  | ChildNode (Node)  | BTREE | id         | yes             | yes                | -        |
| parent_node_id | ParentNode (Node) | BTREE | id         | yes             | yes                | -        |
| simple_node_id | SimpleNode (Node) | BTREE | id         | yes             | yes                | -        |
+----------------+-------------------+-------+------------+-----------------+--------------------+----------+
```

## Index Purge

Command for purging/deleting all indices.

### Example

```bash
php bin/console neo4j-sync:index:purge
```

```text
Purge all indices? (yes/NO): y
Purging all indices...


[OK] Index purge completed

```

## Index Sync

Command for synchronizing all indices.

### Example

```bash
php bin/console neo4j-sync:index:sync
```

```text
Found 3 indices:
+----------------+-------------------+-------+------------+
| Name           | On                | Type  | Properties |
+----------------+-------------------+-------+------------+
| child_node_id  | ChildNode (Node)  | BTREE | id         |
| parent_node_id | ParentNode (Node) | BTREE | id         |
| simple_node_id | SimpleNode (Node) | BTREE | id         |
+----------------+-------------------+-------+------------+
Continue and create 3 indices? (yes/NO): y
Creating indices... done
```
