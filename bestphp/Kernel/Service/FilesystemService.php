<?php


namespace Best\Kernel\Service;


use Best\File\Filesystem;
use Best\Filesystem\Stream;
use Best\Filesystem\UploadedFile;
use Best\Kernel\Service\Contract\Service;

class FilesystemService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function run()
    {
        $this->app->bind([
            'filesystem'    => Filesystem::class,
            'stream'        => Stream::class,
            'uploaded_file' => UploadedFile::class
        ]);
    }
}