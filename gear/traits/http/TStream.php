<?php

namespace gear\traits\http;

trait TStream
{
    /* Protected */
    protected static $_readWriteModes = [
        'readable' => [
            'r', 'rb', 'r+', 'r+b',
            'w+', 'w+b',
            'a+', 'a+b',
            'c+', 'c+b',
            'x+b', 'x+',
        ],
        'writable' => [
            'r+', 'r+b', 'rw',
            'w', 'wb', 'w+', 'w+b',
            'a', 'ab', 'a+', 'a+b',
            'c', 'cb', 'c+', 'c+b',
            'x', 'xb', 'x+', 'x+b',
        ],
    ];
    protected $_stream = null;
    protected $_metadata = [];
    /* Public */

    public function __construct($stream = null)
    {
        if ($stream)
            $this->attach($stream);
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {
            return (string)$this->getContents(null, 0);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function isAttached()
    {
        return is_resource($this->_stream);
    }

    public function attach($stream)
    {
        if (is_resource($stream) === false)
            throw new InvalidArgumentException(__METHOD__ . ' argument must be a valid PHP resource');
        if ($this->isAttached() === true) {
            $this->detach();
        }
        $this->_stream = $stream;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if ($this->isAttached()) {
            fclose($this->_stream);
            $this->detach();
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
        $this->_stream = null;
        $this->_metadata = [];
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->isAttached()) {
            clearstatcache();
            $stat = fstat($this->_stream);
        }
        return isset($stat) && isset($stat['size']) ? $stat['size'] : null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$this->isAttached() || ($pos = ftell($this->_stream)) === false) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }
        return $pos;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->isAttached() ? feof($this->_stream) : true;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return (bool)$this->getMetadata('seekable');
    }

    /**
     * Seek to a position in the stream.
     *
     * @see http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable())
            throw new \RuntimeException('Stream is not seekable');
        if (fseek($this->_stream, $offset, $whence) === -1)
            throw new \RuntimeException('Unable to seek to stream position ' . $offset . ' with whence ' . var_export($whence, true));
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        $writable = false;
        if ($this->isAttached()) {
            $mode = $this->getMetadata('mode');
            $writable = in_array($mode, self::$_readWriteModes['writable']);
        }
        return $writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this->isAttached())
            throw new \RuntimeException('Unable write to not attached stream');
        if (!$this->isWritable())
            throw new \RuntimeException('Cannot write to non-writable stream');
        if (($written = fwrite($this->_stream, $string)) ==false)
            throw new RuntimeException('Could not write to stream');
        return $written;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        $readable = false;
        if ($this->isAttached()) {
            $mode = $this->getMetadata('mode');
            $readable = in_array($mode, self::$_readWriteModes['readable']);
        }
        return $readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!$this->isAttached())
            throw new \RuntimeException('Unable to read from not attached stream');
        if (!$this->isReadable())
            throw new \RuntimeException('Cannot read from non-readable stream');
        return fread($this->_stream, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read.
     * @throws \RuntimeException if error occurs while reading.
     */
    public function getContents($maxLength = null, $offset = null)
    {
        $contents = stream_get_contents($this->_stream, $maxLength, $offset);
        if ($contents === false) {
            throw new \RuntimeException('Unable to read stream contents');
        }
        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $this->_metadata = stream_get_meta_data($this->_stream);
        return $key === null ? $this->_metadata : (isset($this->meta[$key]) ? $this->meta[$key] : null);
    }
}