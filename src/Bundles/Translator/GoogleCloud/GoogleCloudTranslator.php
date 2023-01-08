<?php

namespace PHPUnuhi\Bundles\Translation\GoogleCloud;

use Google\Cloud\Translate\V2\TranslateClient;
use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class GoogleCloudTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @return string
     */
    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Please provide your API key for GoogleCloud');
        }

        $translate = new TranslateClient([
            'key' => $this->apiKey
        ]);

        $result = $translate->translate(
            $text,
            [
                'target' => $targetLanguage
            ]
        );

        if (!isset($result['text'])) {
            return '';
        }

        return (string)$result['text'];
    }

}
