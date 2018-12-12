<?php


namespace Best\Http\Foundation;


class UploadedFile
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $tmpName;

    /**
     * @var int
     */
    private $error;

    /**
     * @var int
     */
    private $size;

    /**
     * @param array $fileInfo
     */
    public function __construct(array $fileInfo)
    {
        $this->name = $fileInfo['name'] ?? null;
        $this->type = $fileInfo['type'] ?? null;
        $this->tmpName = $fileInfo['tmp_name'] ?? null;
        $this->size = $fileInfo['size'] ?? null;
        $this->error = $fileInfo['error'] ?? null;
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * @return Stream|Stream
     */
    public function getStream()
    {
        return new Stream($this->tmpName, 'r');
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * @return null|string The media type sent by the client or null if none
     *     was provided.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieve the temporary storage of file sent by the client
     *
     * @return mixed
     */
   public function getTmpName()
   {
       return $this->tmpName;
   }

    /**
     * Move the uploaded file to a new location.
     *
     * @param string $targetPath
     * @return bool
     */
    public function moveTo($targetPath)
    {
        return move_uploaded_file($this->tmpName, $targetPath);
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * @return int|string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Retrieve the file size.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size ?: filesize($this->tmpName) ?: null;
    }
}