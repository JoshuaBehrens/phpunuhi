<?php

namespace PHPUnuhi\Models\Translation;

class Locale
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filename;


    /**
     * @var Translation[]
     */
    private $translations;


    /**
     * @param string $name
     * @param string $filename
     */
    public function __construct(string $name, string $filename)
    {
        $this->name = $name;
        $this->filename = $filename;

        $this->translations = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getExchangeIdentifier(): string
    {
        $id = "";

        if (!empty($this->name)) {
            $id = $this->name;
        } else {
            $id = basename($this->filename);
        }

        # we use this also in technical environments
        # such as class and id names in HTML
        # so we need to remove spaces
        $id = str_replace(' ', '-', $id);

        return $id;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addTranslation(string $key, string $value): void
    {
        $this->translations[] = new Translation(
            $key,
            $value
        );
    }

    /**
     * @param array|Translation[] $translations
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return Translation[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @return array<string>
     */
    public function getTranslationKeys(): array
    {
        $keys = [];

        foreach ($this->getTranslations() as $translation) {
            if (!in_array($translation->getKey(), $keys)) {
                $keys[] = $translation->getKey();
            }
        }

        return $keys;
    }

}

