<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Syndesi\Neo4jSyncBundle\Normalizer\Neo4jObjectNormalizer;
use Laudis\Neo4j\Types as Neo4j;

final class Neo4jObjectNormalizerTest extends TestCase
{

    public function supportedClassesObjectProvider(): array
    {
        return [
            [new DateTime()],
            [DateInterval::createFromDateString('1 day')],
            [new Neo4j\Date(1)],
            [new Neo4j\Time(1, 0)],
            [new Neo4j\LocalTime(1)],
            [new Neo4j\DateTime(1, 1, 0)],
            [new Neo4j\DateTimeZoneId(1, 1, 'UTC')],
            [new Neo4j\LocalDateTime(1, 1)],
            [new Neo4j\Duration(1, 1, 1, 1)],
            [new Neo4j\Node(1, new Neo4j\CypherList([]), new Neo4j\CypherMap([]))],
            [new Neo4j\Relationship(1, 1, 1, 'TYPE', new Neo4j\CypherMap([]))],
            [new Neo4j\Path(new Neo4j\CypherList([]), new Neo4j\CypherList([]), new Neo4j\CypherList([]))]
        ];
    }

    public function unsupportedClassesObjectProvider(): array
    {
        return [
            ['string'],
            [123],
            [12.3],
            [[]]
        ];
    }

    public function testCheckSupportedNormalizationClassesLength(){
        $this->assertCount(12, Neo4jObjectNormalizer::SUPPORTED_CLASSES);
    }

    /**
     * @dataProvider supportedClassesObjectProvider
     */
    public function testSupportsNormalizationTrue($object){
        $normalizer = new Neo4jObjectNormalizer();
        $this->assertTrue($normalizer->supportsNormalization($object));
        $this->assertSame($object, $normalizer->normalize($object));
    }

    /**
     * @dataProvider unsupportedClassesObjectProvider
     */
    public function testSupportsNormalizationFalse($object){
        $normalizer = new Neo4jObjectNormalizer();
        $this->assertFalse($normalizer->supportsNormalization($object));
        $this->expectException(InvalidArgumentException::class);
        $this->assertSame($object, $normalizer->normalize($object));
    }

}
