<?php

namespace App\View;

use App\Entity\EBaseEntity;

/**
 * Normalizza i dati al confine con Smarty.
 */
class ViewDataNormalizer
{
    public function normalize(mixed $value): mixed
    {
        if ($value instanceof EBaseEntity) {
            return $this->normalize($value->toArray());
        }

        if (!is_array($value)) {
            return $value;
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            $normalized[$key] = $this->normalize($item);
        }

        return $this->normalizePublicPaths($normalized);
    }

    public function publicPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === ''
            || str_starts_with($path, '/')
            || preg_match('#^(https?:)?//#i', $path)
            || str_starts_with($path, 'data:')
        ) {
            return $path;
        }

        if (str_starts_with($path, 'uploads/') || str_starts_with($path, 'assets/')) {
            return '/' . $path;
        }

        return $path;
    }

    private function normalizePublicPaths(array $data): array
    {
        foreach ($data as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                continue;
            }

            if (in_array($key, ['url', 'propic', 'immagine_principale'], true)) {
                $data[$key] = $this->publicPath($value);
            }
        }

        return $data;
    }
}
