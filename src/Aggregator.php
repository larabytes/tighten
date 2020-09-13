<?php

namespace Aggregators\Tighten;

use Carbon\Carbon;
use InvalidArgumentException;
use Aggregators\Support\BaseAggregator;
use Symfony\Component\DomCrawler\Crawler;

class Aggregator extends BaseAggregator
{
    /**
     * {@inheritDoc}
     */
    public string $uri = 'https://tighten.co/blog/';

    /**
     * {@inheritDoc}
     */
    public string $provider = 'Tighten';

    /**
     * {@inheritDoc}
     */
    public string $logo = 'logo.jpg';

    /**
     * {@inheritDoc}
     */
    public function articleIdentifier(): string
    {
        return 'div.post-preview-wrapper.post-preview-wrapper--wide';
    }

    /**
     * {@inheritDoc}
     */
    public function nextUrl(Crawler $crawler): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function image(Crawler $crawler): ?string
    {
        try {
            $imageUrl = trim(str_replace(['background-image: url("', '");', 'background-image: url(', ');'], '', $crawler->filter('div.post-preview-thumbnail__background')->attr('style')));
            if (strpos($imageUrl, '.jpg') !== false) $extension = '.jpg';
            if (strpos($imageUrl, '.jpeg') !== false) $extension = '.jpeg';
            if (strpos($imageUrl, '.png') !== false) $extension = '.png';
            if (strpos($imageUrl, '.gif') !== false) $extension = '.gif';
            if (strpos($imageUrl, '.svg') !== false) $extension = '.svg';
            return explode($extension, $imageUrl)[0] . $extension;
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function title(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter('div.post-preview__title')->text();
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function content(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter('div.post-preview__snippet')->text();
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function link(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter('div.post-meta__share-url')->text();
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function dateCreated(Crawler $crawler): Carbon
    {
        try {
            return Carbon::parse($crawler->filter('time')->attr('datetime'));
        } catch (InvalidArgumentException $e) {
            return Carbon::now();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function dateUpdated(Crawler $crawler): Carbon
    {
        return $this->dateCreated($crawler);
    }
}
