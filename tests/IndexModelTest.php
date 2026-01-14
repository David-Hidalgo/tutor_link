<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class IndexModelTest extends TestCase
{
    protected function setUp(): void
    {
        Database::$lastInstance = null;
    }

    public function testGetPostsReturnsFetchAll(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();

        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $expected = [
            ['id' => 1, 'title' => 'a'],
            ['id' => 2, 'title' => 'b'],
        ];
        $db->setQueryResult('select * from posts', new DummyStatement(false, $expected));

        $rows = $model->getPosts();
        $this->assertSame($expected, $rows);
    }

    public function testGetPostCastsIdToIntInQuery(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = 'select * from posts where id=5';
        $expectedRow = ['id' => 5, 'title' => 'hola'];
        $db->setQueryResult($sql, new DummyStatement($expectedRow));

        $row = $model->getPost('5 OR 1=1');
        $this->assertSame($expectedRow, $row);

        $this->assertNotEmpty($db->queries);
        $this->assertSame($sql, end($db->queries));
    }

    public function testInsertPostUsesPrepareAndExecute(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->insertPost('t', 'b');

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];

        $this->assertSame('insert into posts VALUES (null, :title, :body)', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':title' => 't', ':body' => 'b'], $stmt->lastParams);
    }

    public function testEditPostCastsIdAndUsesPlaceholders(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->editPost('7abc', 'new', 'body');

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];

        $this->assertSame('UPDATE posts SET title= :title, body= :body WHERE id= :id', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':id' => 7, ':title' => 'new', ':body' => 'body'], $stmt->lastParams);
    }

    public function testDeletePostCastsIdInQuery(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->deletePost('9xyz');

        $this->assertNotEmpty($db->queries);
        $this->assertSame('DELETE FROM posts WHERE id = 9', end($db->queries));
    }
}
