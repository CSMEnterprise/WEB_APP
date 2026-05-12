<?php

require_once __DIR__ . '/ServiceException.php';

class UploadService
{
    private string $targetDir;
    private array $allowedMimeTypes;
    private int $maxSizeBytes;

    public function __construct(string $targetDir, array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'], int $maxSizeBytes = 2097152)
    {
        $this->targetDir = rtrim($targetDir, DIRECTORY_SEPARATOR);
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSizeBytes = $maxSizeBytes;
    }

    public function uploadImage(array $file, string $publicPrefix = '/images/uploads'): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new ServiceException('Errore durante il caricamento del file.');
        }

        if (($file['size'] ?? 0) > $this->maxSizeBytes) {
            throw new ServiceException('File troppo grande.');
        }

        $tmpName = $file['tmp_name'] ?? '';
        $mimeType = mime_content_type($tmpName);

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw new ServiceException('Formato immagine non consentito.');
        }

        if (!is_dir($this->targetDir) && !mkdir($this->targetDir, 0775, true)) {
            throw new ServiceException('Impossibile creare la cartella di upload.');
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'bin',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $this->targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new ServiceException('Impossibile salvare il file caricato.');
        }

        return rtrim($publicPrefix, '/') . '/' . $filename;
    }
}
