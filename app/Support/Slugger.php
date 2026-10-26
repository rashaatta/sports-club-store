<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Slugger
{
    /**
     * @param  class-string<Model>  $model
     */
    public static function unique(string $source, string $model, ?int $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($source), 220, '');
        $base = trim($base, '-');

        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $suffix = 2;

        while (
            $model::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
