<?php

declare(strict_types=1);

namespace AsgrimUnitTest\YamlDb;

use Asgrim\YamlDb\YamlDb;
use Asgrim\YamlDb\YamlId;
use PHPUnit\Framework\TestCase;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function sprintf;
use function unlink;

final class YamlDbTest extends TestCase
{
    /** @var string */
    private $filename;

    /** @var YamlDb */
    private $yamlDb;

    public function setUp() : void
    {
        parent::setUp();

        $this->filename = 'test.ydb';
        $this->yamlDb   = new YamlDb($this->filename);
    }

    public function tearDown() : void
    {
        parent::tearDown();

        if (! file_exists($this->filename)) {
            return;
        }

        unlink($this->filename);
    }

    public function testInsert() : void
    {
        $id = $this->yamlDb->insert(['foo' => 'bar']);

        self::assertSame(
            <<<DOC
{$id->asString()}:
    foo: bar

DOC,
            file_get_contents($this->filename)
        );
    }

    public function testFindById() : void
    {
        $id = YamlId::new();

        file_put_contents($this->filename, sprintf("%s:\n    foo: bar\n", $id->asString()));

        self::assertEquals(
            ['foo' => 'bar'],
            $this->yamlDb->findById($id)
        );
    }
}
