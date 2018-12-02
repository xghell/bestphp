<?php


namespace Best\Foundation\Http;


class Stream
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var bool|resource 
     */
    private $stream;

    public function __construct(string $file, string $mode, bool $use_include_path = false, resource $context = null)
    {
        $this->file = $file;
        $this->mode = $mode;
        if (!is_null($context)) {
            $this->stream = fopen($file, $mode, $use_include_path, $context);
        }
        $this->stream = fopen($file, $mode, $use_include_path);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * @return string
     */
    public function __toString()
    {
        return stream_get_contents($this->stream, -1, 0);
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (fclose($this->stream)) {
            $this->stream = null;
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        // TODO
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        return filesize($this->file) ?: null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     */
    public function tell()
    {
        return ftell($this->stream);
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->getMetadata('seekable');
    }

    /**
     * Seek to a position in the stream.
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->stream, $offset, $whence);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * @return bool
     */
    public function rewind()
    {
        return rewind($this->stream);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if (in_array($this->mode, ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'])) {
            return true;
        }
        return false;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     */
    public function write($string)
    {
        return fwrite($this->stream, $string);
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        if (!in_array($this->mode, ['r', 'r+', 'w+', 'a+', 'x+', 'c+'])) {
            return true;
        }
        return false;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     */
    public function read($length)
    {
        return fread($this->stream, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     */
    public function getContents()
    {
        return stream_get_contents($this->stream);
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata(string $key = '')
    {
        $meta_data = stream_get_meta_data($this->stream);
        if ('' === trim($key)) {
            return $meta_data;
        }
        return $meta_data[$key] ?? null;
    }
}