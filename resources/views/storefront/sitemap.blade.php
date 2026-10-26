<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('products.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ route('faq') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    @foreach ($categories as $category)
        <url>
            <loc>{{ route('categories.show', $category) }}</loc>
            @if ($category->updated_at)
                <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach ($products as $product)
        <url>
            <loc>{{ route('products.show', $product) }}</loc>
            @if ($product->updated_at)
                <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach

    @foreach ($pages as $page)
        @continue($page->slug === 'about-us')
        <url>
            <loc>{{ route('pages.show', $page) }}</loc>
            @if ($page->updated_at)
                <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
        </url>
    @endforeach
</urlset>
