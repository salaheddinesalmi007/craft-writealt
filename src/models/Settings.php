<?php

namespace writealt\models;

use craft\base\Model;
use craft\helpers\App;

class Settings extends Model
{
    public string $apiKey = '';
    public string $language = 'en';
    public int $styleId = 2;
    public string $keywords = '';
    public float $thresholdMb = 0.5;

    public function getResolvedApiKey(): string
    {
        return trim((string)App::parseEnv($this->apiKey));
    }

    protected function defineRules(): array
    {
        return [
            [['apiKey', 'language', 'keywords'], 'string'],
            [['styleId'], 'integer', 'min' => 1, 'max' => 5],
            [['thresholdMb'], 'number', 'min' => 0.01, 'max' => 100],
        ];
    }
}
