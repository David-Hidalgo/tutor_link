<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class UploadClassTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'tutor_link_tests_' . bin2hex(random_bytes(6));
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $this->rrmdir($this->tmpDir);
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    public function testLocalFileConstructorAndProcessReturnsContent(): void
    {
        if (!class_exists('upload')) {
            $this->markTestSkipped('class upload not loaded (libs/class.upload.php missing).');
        }

        $content = "hola\nmundo\n";
        $file = $this->tmpDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, $content);

        $u = new upload($file);

        $this->assertTrue($u->uploaded);
        $this->assertTrue($u->no_upload_check);
        $this->assertSame('test.txt', $u->file_src_name);
        $this->assertSame(strlen($content), (int)$u->file_src_size);

        $returned = $u->process(null);
        $this->assertSame($content, $returned);
        $this->assertTrue($u->processed);
    }

    public function testProcessWritesFileToDestinationDirectory(): void
    {
        if (!class_exists('upload')) {
            $this->markTestSkipped('class upload not loaded (libs/class.upload.php missing).');
        }

        $content = "archivo destino\n";
        $file = $this->tmpDir . DIRECTORY_SEPARATOR . 'source.txt';
        file_put_contents($file, $content);

        $dest = $this->tmpDir . DIRECTORY_SEPARATOR . 'out';
        $u = new upload($file);

        $u->file_new_name_body = 'copied_' . bin2hex(random_bytes(4));
        $u->process($dest);

        $this->assertTrue($u->processed, $u->error ?? 'process() failed');
        $expectedPath = $dest . DIRECTORY_SEPARATOR . $u->file_dst_name;

        $this->assertNotSame('', $u->file_dst_name);
        $this->assertFileExists($expectedPath);
        $this->assertSame($content, file_get_contents($expectedPath));
    }
}
