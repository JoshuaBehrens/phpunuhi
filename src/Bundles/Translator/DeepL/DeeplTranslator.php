<?php

namespace PHPUnuhi\Bundles\Translation\DeepL;

use PHPUnuhi\Bundles\Translation\TranslatorInterface;

class DeeplTranslator implements TranslatorInterface
{

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $formality;


    /**
     * @param string $apiKey
     * @param bool $formality
     */
    public function __construct(string $apiKey, bool $formality)
    {
        $this->apiKey = $apiKey;
        $this->formality = $formality;
    }


    /**
     * @param string $text
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return string
     * @throws \DeepL\DeepLException
     */
    public function translate(string $text, string $sourceLocale, string $targetLocale): string
    {
        $formalValue = ($this->formality) ? 'more' : 'less';

        if (empty($this->apiKey)) {
            throw new \Exception('Please provide your API key for DeepL');
        }

        $translator = new \DeepL\Translator($this->apiKey);

        if ($targetLocale === 'en') {
            $targetLocale = 'en-GB';
        }

        $result = $translator->translateText(
            $text,
            null,
            $targetLocale,
            [
                'formality' => $formalValue,
            ]
        );

        if (is_array($result)) {
            return $result[0]->text;
        }

        return $result->text;
    }

}