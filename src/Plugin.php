<?php

namespace writealt;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use writealt\models\Settings;
use writealt\services\AssetService;
use writealt\services\LanguageCatalog;
use writealt\services\WriteAltClient;
use yii\base\Event;

class Plugin extends BasePlugin
{
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;

    public static function displayName(): string
    {
        return 'WriteAlt - AI Alt Text & SEO';
    }

    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        if ($item !== null) {
            unset($item['icon']);
        }
        return $item;
    }

    public function init(): void
    {
        parent::init();

        $this->setComponents([
            'client' => WriteAltClient::class,
            'assetService' => AssetService::class,
        ]);

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, static function (RegisterUrlRulesEvent $event): void {
            $event->rules['writealt'] = ['template' => 'writealt/index.twig'];
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('writealt/settings', [
            'settings' => $this->getSettings(),
            'languageOptions' => array_map(
                static fn(string $label, string $value): array => ['label' => $label, 'value' => $value],
                LanguageCatalog::languages(),
                array_keys(LanguageCatalog::languages())
            ),
            'styleOptions' => array_map(
                static fn(string $label, int|string $value): array => ['label' => $label, 'value' => $value],
                LanguageCatalog::styles(),
                array_keys(LanguageCatalog::styles())
            ),
        ]);
    }
}
