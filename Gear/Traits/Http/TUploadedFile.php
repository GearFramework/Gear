<?php

namespace Gear\Traits\Http;

use Psr\Http\Message\StreamInterface;

trait TUploadedFile
{
    /* Protected */
    protected $_moved = false;
    protected $_clientFilename = null;
    protected $_error = UPLOAD_ERR_OK;
    protected $_io = null;
    protected $_size = 0;
    protected $_clientMediaType = null;
    /* Public */

    public function __construct($io, $clientFilename, $clientMediaType = null, $size = null, $error = UPLOAD_ERR_OK)
    {
        $this->_io = $io;
        $this->_clientFilename = $clientFilename;
        $this->_clientMediaType = $clientMediaType;
        $this->_size = $size;
        $this->_error = $error;
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws \RuntimeException in cases when no stream is available or can be
     *     created.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getStream(): StreamInterface
    {
        // TODO: Implement getStream() method.
    }

    public function isMoved(): bool
    {
        return $this->_moved;
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws \InvalidArgumentException if the $targetPath specified is invalid.
     * @throws \RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function moveTo(string $targetPath)
    {
        if ($this->isMoved()) {
            throw new RuntimeException('Uploaded file already moved');
        }
        if ($targetPath instanceof IIo) {
            if (!$targetPath->isWritable())
                throw new RuntimeException('Target path is not writable');
        } else if (is_string($targetPath)) {
            if (!is_writable($targetPath))
                throw new RuntimeException('Target path is not writable');
        }
        $targetIsStream = strpos($targetPath, '://') > 0;
        if (!$targetIsStream && !is_writable(dirname($targetPath))) {
            throw new InvalidArgumentException('Upload target path is not writable');
        }
        if ($targetIsStream) {
            if (!copy($this->file, $targetPath)) {
                throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $targetPath));
            }
            if (!unlink($this->file)) {
                throw new RuntimeException(sprintf('Error removing uploaded file %1s', $this->name));
            }
        } elseif ($this->sapi) {
            if (!is_uploaded_file($this->file)) {
                throw new RuntimeException(sprintf('%1s is not a valid uploaded file', $this->file));
            }
            if (!move_uploaded_file($this->file, $targetPath)) {
                throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $targetPath));
            }
        } else {
            if (!rename($this->file, $targetPath)) {
                throw new RuntimeException(sprintf('Error moving uploaded file %1s to %2s', $this->name, $targetPath));
            }
        }
        $this->_moved = true;
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getError(): int
    {
        return $this->_error;
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClientFilename()
    {
        return $this->_clientFilename;
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getClientMediaType()
    {
        return $this->_clientMediaType;
    }
}