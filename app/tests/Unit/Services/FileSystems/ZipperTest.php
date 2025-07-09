<?php

namespace Tests\Unit\Services\FileSystems;

use App\Services\FileSystems\Zipper;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class ZipperTest extends TestCase
{
    protected string $testFolder = 'test/zipper_test';
    protected string $zipPath = 'zipper_test.zip';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::makeDirectory($this->testFolder);
        Storage::put($this->testFolder.'/file1.txt', 'Content 1');
        Storage::makeDirectory($this->testFolder.'/subfolder');
        Storage::put($this->testFolder.'/subfolder/file2.txt', 'Content 2');
    }

    protected function tearDown(): void
    {
        Storage::deleteDirectory($this->testFolder);
        Storage::delete($this->zipPath);
        parent::tearDown();
    }

    public function test_creates_zip_with_folder_content()
    {
        $zipper = new Zipper();
        $source = Storage::path($this->testFolder);
        $destination = Storage::path($this->zipPath);

        $result = $zipper->zipFolder($source, $destination);

        $this->assertTrue($result);
        $this->assertFileExists($destination);

        $zip = new \ZipArchive();
        $zip->open($destination);

        $this->assertEquals('Content 1', $zip->getFromName('file1.txt'));
        $this->assertEquals('Content 2', $zip->getFromName('subfolder/file2.txt'));
        $this->assertNotFalse($zip->locateName('subfolder/'));
        
        $zip->close();
    }

    public function test_handles_single_file()
    {
        $singleFilePath = $this->testFolder.'_single/file.txt';
        Storage::put($singleFilePath, 'Single file content');

        $zipper = new Zipper();
        $result = $zipper->zipFolder(
            Storage::path($singleFilePath),
            Storage::path($this->zipPath)
        );

        $this->assertTrue($result);
        
        $zip = new \ZipArchive();
        $zip->open(Storage::path($this->zipPath));
        $this->assertEquals('Single file content', $zip->getFromName('file.txt'));
        $zip->close();

        Storage::delete($singleFilePath);
    }

    public function test_returns_false_for_invalid_source()
    {
        $zipper = new Zipper();
        $result = $zipper->zipFolder(
            '/invalid/path',
            Storage::path($this->zipPath)
        );

        $this->assertFalse($result);
    }
}