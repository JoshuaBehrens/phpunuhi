<?php

namespace PHPUnuhi\Bundles;

use PHPUnuhi\Models\Translation\TranslationSet;

interface ValidationInterface
{

    /**
     * @param TranslationSet $set
     * @return bool
     */
    function validateStructure(TranslationSet $set): bool;

    /**
     * @param TranslationSet $set
     * @return bool
     */
    function validateEmptyTranslations(TranslationSet $set): bool;

}
