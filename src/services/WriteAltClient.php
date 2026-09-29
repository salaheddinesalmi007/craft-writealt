<?php

namespace writealt\services;

use GuzzleHttp\Client;
use RuntimeException;
use writealt\Plugin;

class WriteAltClient
{
    private const BASE_URL = 'https://writealt.com/api/v1/';

    private function client(): Client
    {
        return new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 90,
            'connect_timeout' => 15,
            'http_errors' => false,
        ]);
    }

    private function apiKey(): string
    {
        $key = Plugin::getInstance()?->getSettings()?->getResolvedApiKey() ?? '';
        if ($key === '') {
            throw new RuntimeException('Add your WriteAlt API key in Settings before using the plugin.');
        }
        return $key;
    }

    private function decode($response): array
    {
        $raw = (string)$response->getBody();
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new RuntimeException('WriteAlt returned an unreadable response.');
        }
        if ($response->getStatusCode() >= 400 || ($data['ok'] ?? true) === false) {
            $message = $data['error'] ?? $data['message'] ?? 'WriteAlt could not complete the request.';
            if (is_array($message)) {
                $message = $message['message'] ?? json_encode($message);
            }
            throw new RuntimeException((string)$message);
        }
        return $data;
    }

    public function credits(): ?int
    {
        $response = $this->client()->get('account.php', [
            'headers' => ['X-API-Key' => $this->apiKey(), 'Accept' => 'application/json'],
        ]);
        $data = $this->decode($response);
        $value = $this->findValue($data, ['credits_remaining', 'creditsRemaining', 'credits_balance', 'creditsBalance', 'balance', 'credits']);
        return is_numeric($value) ? (int)$value : null;
    }

    public function generate(string $filePath, string $language, int $styleId, string $keywords = ''): string
    {
        $response = $this->client()->post('generate.php', [
            'headers' => ['X-API-Key' => $this->apiKey(), 'Accept' => 'application/json'],
            'multipart' => [
                ['name' => 'image', 'contents' => fopen($filePath, 'rb'), 'filename' => basename($filePath)],
                ['name' => 'languages', 'contents' => $language],
                ['name' => 'style_id', 'contents' => (string)$styleId],
                ['name' => 'keywords', 'contents' => $keywords],
            ],
        ]);
        $data = $this->decode($response);
        $alt = $this->findValue($data, ['alt_text', 'altText', 'description', 'text', 'result']);
        if (is_array($alt)) {
            $alt = $alt[$language] ?? $alt['alt_text'] ?? $alt['description'] ?? reset($alt);
        }
        if (!is_string($alt) || trim($alt) === '') {
            throw new RuntimeException('WriteAlt returned no alt text for this image.');
        }
        return trim($alt);
    }

    private function findValue(mixed $value, array $keys): mixed
    {
        if (!is_array($value)) {
            return null;
        }
        foreach ($keys as $key) {
            if (array_key_exists($key, $value) && $value[$key] !== null && $value[$key] !== '') {
                return $value[$key];
            }
        }
        foreach ($value as $child) {
            $found = $this->findValue($child, $keys);
            if ($found !== null) {
                return $found;
            }
        }
        return null;
    }
}
