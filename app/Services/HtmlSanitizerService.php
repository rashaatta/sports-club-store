<?php

namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlSanitizerService
{
    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowAttribute('rel', allowedElements: ['a'])
            ->allowAttribute('target', allowedElements: ['a'])
            ->withMaxInputLength(100000);

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public function clean(?string $html): string
    {
        return $this->sanitizer->sanitize($html ?? '');
    }
}
