<?php

namespace Database\Seeders\Concerns;

use App\Services\MediaService;

trait ImportsSeedMedia
{
    protected function importSeedImage(string $file, string $directory): string
    {
        return app(MediaService::class)->storeImageFromPath(
            database_path('seeders/assets/images/'.$file),
            $directory,
        );
    }
}
