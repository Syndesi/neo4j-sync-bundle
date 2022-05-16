# Home

This bundle provides real time synchronization capabilities between Doctrine's EntityManager and a Neo4j database.

See also [Neo4j's Graph database concepts](https://neo4j.com/docs/getting-started/current/graphdb-concepts/) for a good
introduction into this topic.

## Data model requirements

In order to work correctly this library has a few requirements on the data model.  
While some of them are not strictly enforced, all of them have the goal to make the synchronized Neo4j database
consistent and not to delete nodes and relations provided by 3rd parties.

### Nodes

- Although nodes in a Neo4j database can have zero or more node labels, this library expects exactly one label per
  managed node.
- Each node is required to contain a unique identifier, e.g. the primary key of the Doctrine entity.

### Relations provided by nodes

- Because the relations are identifiable by the providing node, their identifier is optional.
- All relations start at the providing nodes and point to other nodes.
- All relations which start at the providing node will be deleted and recreated when the providing node is updated.

### Independent Relations

- Independent relations must contain an identifier.

### Indices

- Indices must be manually created and synced.

