<?php

namespace App\Core;

/**
 * Incapsula una singola voce di $_FILES.
 */
final class UploadedFile
{
    public function __construct(
        private readonly string $name,
        private readonly string $tmpName,
        private readonly int $error,
        private readonly int $size
    ) {
    }

    public static function fromArray(array $file): self
    {
        return new self(
            (string) ($file['name'] ?? ''),
            (string) ($file['tmp_name'] ?? ''),
            (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE),
            (int) ($file['size'] ?? 0)
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function tmpName(): string
    {
        return $this->tmpName;
    }

    public function error(): int
    {
        return $this->error;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function wasUploaded(): bool
    {
        return $this->error !== UPLOAD_ERR_NO_FILE;
    }

    public function isOk(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    public function mimeType(): string
    {
        if ($this->tmpName === '' || !is_file($this->tmpName)) {
            return '';
        }

        return (new \finfo(FILEINFO_MIME_TYPE))->file($this->tmpName) ?: '';
    }

    public function moveTo(string $destination): bool
    {
        return move_uploaded_file($this->tmpName, $destination);
    }
}
