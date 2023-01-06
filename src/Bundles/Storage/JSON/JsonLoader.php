<?php

namespace PHPUnuhi\Bundles\Storage\JSON;

use PHPUnuhi\Bundles\Storage\StorageLoaderInterface;
use PHPUnuhi\Models\Translation\Locale;


class JsonLoader implements StorageLoaderInterface
{

    /**
     * @param Locale $locale
     * @return void
     * @throws \Exception
     */
    function loadTranslations(Locale $locale): void
    {
        if (!file_exists($locale->getFilename())) {
            throw new \Exception('Attention, translation file not found: ' . $locale->getFilename());
        }

        $snippetJson = (string)file_get_contents($locale->getFilename());

        $foundTranslations = [];

        if (!empty($snippetJson)) {
            $foundTranslations = json_decode($snippetJson, true);

            if ($foundTranslations === false) {
                $foundTranslations = [];
            }
        }

        $foundTranslationsFlat = $this->getFlatArray($foundTranslations);

        foreach ($foundTranslationsFlat as $key => $value) {
            $locale->addTranslation($key, $value);
        }
    }

    /**
     * @param array<mixed> $array
     * @param string $prefix
     * @return array<string>
     */
    private function getFlatArray(array $array, string $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->getFlatArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

}