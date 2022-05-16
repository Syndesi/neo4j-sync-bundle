# Defining Nodes

For automatically synchronizing Doctrine entities as nodes into the Neo4j database, the Doctrine entity is required to
have an attribute implementing `Syndesi\Neo4jSyncBundle\Contract\NodeAttributeInterface`.

## Default Attribute

The default attribute is `Syndesi\Neo4jSyncBundle\Attribute\Node`. It can be initialized with different properties,
which results in quite flexible configurations.

### NodeLabelProvider

The first parameter of the node attribute's constructor is the NodeLabelProvider. It just returns a `NodeLabel` when
called with an entity instance.

Options:

- `Syndesi\Neo4jSyncBundle\Provider\StaticNodeLabelProvider`: Provides a static `NodeLabel`.

### PropertiesProviderInterface

The second parameter of the node attribute's constructor is the PropertiesProviderInterface. It returns an array
containing `Property`-elements when called with an entity instance.

Options:

- `Syndesi\Neo4jSyncBundle\Provider\StaticPropertiesProvider`: Provides static properties, therefore all created nodes
  have the same properties.
- `Syndesi\Neo4jSyncBundle\Provider\SerializerPropertiesProvider`: Provides dynamic properties by serializing the
  provided entity. Uses the serialization group `neo4j` by default.

### IdentifierProviderInterface

The third parameter of the node attribute's constructor is the IdentifierProviderInterface. It returns a single
`Property` whose value part is ignored. It basically contains only the name of the property which defines the node's
identifier.

Options:

- `Syndesi\Neo4jSyncBundle\Provider\StaticIdentifierProvider`: Provides a static identifier name.
- `Syndesi\Neo4jSyncBundle\Provider\SerializerIdentifierProvider`: Provides a dynamic identifier name, uncommon for nodes.

### RelationAttributeFromNodeInterface[]

The last parameter of the node attribute's constructor is an array of RelationAttributeFromNodeInterfaces. It is
optional and can be used to define node dependent relations.
