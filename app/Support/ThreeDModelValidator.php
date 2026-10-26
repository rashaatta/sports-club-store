<?php

namespace App\Support;

final class ThreeDModelValidator
{
    public static function isValid(string $path, string $extension): bool
    {
        if (! is_file($path) || ! is_readable($path)) {
            return false;
        }

        return match (strtolower($extension)) {
            'glb' => self::isValidGlb($path),
            'gltf' => self::isValidGltf($path),
            default => false,
        };
    }

    private static function isValidGlb(string $path): bool
    {
        $handle = @fopen($path, 'rb');

        if ($handle === false) {
            return false;
        }

        try {
            $header = fread($handle, 20);

            if ($header === false || strlen($header) < 20 || substr($header, 0, 4) !== 'glTF') {
                return false;
            }

            $version = unpack('V', substr($header, 4, 4))[1] ?? 0;
            $totalLength = unpack('V', substr($header, 8, 4))[1] ?? 0;
            $chunkLength = unpack('V', substr($header, 12, 4))[1] ?? 0;
            $chunkType = substr($header, 16, 4);
            $fileSize = filesize($path);

            if (
                $version !== 2
                || $fileSize === false
                || $totalLength !== $fileSize
                || $chunkType !== 'JSON'
                || $chunkLength < 2
                || $chunkLength > $fileSize - 20
            ) {
                return false;
            }

            $json = fread($handle, $chunkLength);

            return $json !== false && self::isValidGltfJson($json);
        } finally {
            fclose($handle);
        }
    }

    private static function isValidGltf(string $path): bool
    {
        $json = @file_get_contents($path);

        return $json !== false && self::isValidGltfJson($json);
    }

    private static function isValidGltfJson(string $json): bool
    {
        $data = json_decode($json, true);

        if (! is_array($data)) {
            return false;
        }

        $version = $data['asset']['version'] ?? null;
        $meshes = $data['meshes'] ?? null;

        return is_string($version)
            && str_starts_with($version, '2.')
            && is_array($meshes)
            && $meshes !== [];
    }
}
