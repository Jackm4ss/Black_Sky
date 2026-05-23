<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class RichContentSanitizer
{
    private ?HTMLPurifier $purifier = null;

    public function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        return trim($this->purifier()->purify($html));
    }

    private function purifier(): HTMLPurifier
    {
        if ($this->purifier instanceof HTMLPurifier) {
            return $this->purifier;
        }

        $cachePath = storage_path('framework/cache/htmlpurifier');

        if (! is_dir($cachePath)) {
            @mkdir($cachePath, 0755, true);
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('HTML.Allowed', implode(',', [
            'p',
            'br',
            'strong',
            'b',
            'em',
            'i',
            'u',
            'h2',
            'h3',
            'h4',
            'ul',
            'ol',
            'li',
            'blockquote',
            'hr',
            'pre',
            'code',
            'a[href|title|target|rel]',
        ]));
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.TargetBlank', true);
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
        ]);
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);

        return $this->purifier = new HTMLPurifier($config);
    }
}
