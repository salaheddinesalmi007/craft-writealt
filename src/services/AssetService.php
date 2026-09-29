<?php

namespace writealt\services;

use Craft;
use craft\elements\Asset;
use craft\helpers\FileHelper;
use RuntimeException;
use Throwable;
use writealt\Plugin;

class AssetService
{
    private function thresholdBytes(): int
    {
        $threshold = (float)Plugin::getInstance()->getSettings()->thresholdMb;
        return (int)round($threshold * 1024 * 1024);
    }

    private function optimizedKey(Asset $asset): string
    {
        return 'writealt.optimized.' . $asset->id;
    }

    private function isOptimized(Asset $asset): bool
    {
        return (bool)Craft::$app->getCache()->get($this->optimizedKey($asset));
    }

    public function listAssets(): array
    {
        $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        $assets = Asset::find()->siteId($siteId)->kind('image')->limit(500)->all();
        return array_map(fn(Asset $asset) => $this->serialize($asset), $assets);
    }

    public function get(int $id): Asset
    {
        $asset = Asset::find()->id($id)->siteId(Craft::$app->getSites()->getCurrentSite()->id)->one();
        if (!$asset instanceof Asset) {
            throw new RuntimeException('The selected Craft asset could not be found.');
        }
        if (!Craft::$app->getUser()->checkPermission('accessCp')) {
            throw new RuntimeException('You do not have permission to edit Craft assets.');
        }
        return $asset;
    }

    public function serialize(Asset $asset): array
    {
        $alt = trim((string)($asset->alt ?? ''));
        $size = (int)($asset->size ?? 0);
        $optimized = $this->isOptimized($asset);
        $previewUrl = '';
        try {
            $previewUrl = (string)($asset->thumbUrl(640) ?? '');
        } catch (Throwable) {
            $previewUrl = '';
        }
        if ($previewUrl === '') {
            try {
                $previewUrl = (string)($asset->getUrl() ?? '');
            } catch (Throwable) {
                $previewUrl = '';
            }
        }
        return [
            'id' => (int)$asset->id,
            'title' => (string)$asset->title,
            'filename' => (string)$asset->filename,
            'url' => $previewUrl,
            'alt' => $alt,
            'size' => $size,
            'sizeLabel' => $this->formatBytes($size),
            'needsOptimization' => !$optimized && $size >= $this->thresholdBytes(),
            'optimized' => $optimized,
            'mimeType' => (string)($asset->mimeType ?? ''),
        ];
    }

    public function generate(int $id): array
    {
        $asset = $this->get($id);
        $settings = Plugin::getInstance()->getSettings();
        $copy = $asset->getCopyOfFile();
        if (!$copy) {
            throw new RuntimeException('Craft could not create a readable copy of this asset.');
        }
        try {
            $alt = Plugin::getInstance()->get('client')->generate(
                $copy,
                $settings->language,
                (int)$settings->styleId,
                $settings->keywords
            );
            $asset->alt = $alt;
            if (!Craft::$app->getElements()->saveElement($asset)) {
                throw new RuntimeException(implode(' ', $asset->getErrorSummary(true)) ?: 'Craft could not save the alt text.');
            }
            return $this->serialize($asset);
        } finally {
            if (is_string($copy) && is_file($copy)) {
                @unlink($copy);
            }
        }
    }

    public function optimize(int $id): array
    {
        $asset = $this->get($id);
        $original = $asset->getContents();
        if ($original === false || $original === '') {
            throw new RuntimeException('Craft could not read this asset.');
        }
        $candidate = $this->encodeSameFormat($original, (string)$asset->mimeType);
        if ($candidate === null) {
            throw new RuntimeException('This server cannot optimize this image format.');
        }
        if (strlen($candidate) >= strlen($original)) {
            return ['asset' => $this->serialize($asset), 'changed' => false, 'message' => 'The current image is already smaller than the optimized version.'];
        }

        $tmp = Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . 'writealt-' . bin2hex(random_bytes(8)) . '-' . $asset->filename;
        try {
            FileHelper::writeToFile($tmp, $candidate);
            if (!Craft::$app->getAssets()->replaceAssetFile($asset, $tmp, $asset->filename, $asset->mimeType)) {
                throw new RuntimeException('Craft could not replace the asset file.');
            }
            Craft::$app->getCache()->set($this->optimizedKey($asset), true, 0);
            return ['asset' => $this->serialize($asset), 'changed' => true, 'message' => 'Image optimized in place.'];
        } finally {
            if (is_file($tmp)) {
                @unlink($tmp);
            }
        }
    }

    private function encodeSameFormat(string $contents, string $mime): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }
        $image = @imagecreatefromstring($contents);
        if (!$image) {
            return null;
        }
        ob_start();
        $ok = match (strtolower($mime)) {
            'image/jpeg', 'image/jpg' => imagejpeg($image, null, 82),
            'image/png' => imagepng($image, null, 8),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, null, 84) : false,
            default => false,
        };
        $result = $ok ? ob_get_clean() : (ob_end_clean() || true ? null : null);
        imagedestroy($image);
        return $result;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1024 * 1024) return round($bytes / 1024) . ' KB';
        return number_format($bytes / 1024 / 1024, 2) . ' MB';
    }
}
