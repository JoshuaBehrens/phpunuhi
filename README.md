![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/ci_pipe.yml/badge.svg)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/boxblinkracer/phpunuhi)
![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/boxblinkracer/phpunuhi/latest)
![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/nightly_build.yml/badge.svg)

Welcome to PHPUnuhi - The easy framework to validate and manage translation files!

Unuhi? This is Hawaiian for "translate" or "translation".
Now that you know this, let's get started!

## Basic Concept

This is a framework that helps you to validate and maintain translation files.
At the moment it only supports JSON based files.

Although it's not dependent on a specific platform, you can use it perfectly with Shopware 6 and other platforms.

For example, Shopware 6 has snippets based on JSON files.
If you develop plugins for this platform, you can build translation suites in PHPUnuhi that contain all your files for the individual languages, like EN, DE, NL, and whatever you support.
PHPUnuhi helps you to make sure you didn't forget any translations, screwed up structures across your language files and even
helps you to export and import your translations.

One of the benefits is, that this framework does not require anything else than your translation files.
This makes it a perfect fit for your CI/CD pipelines.

## Installation

PHPUnuhi is based on PHP. So you need to have PHP installed.

### PHAR File

PHPUnuhi is available as `phar` file.
Just download the ZIP file, extract it and you are ready to go.

```
curl -O https://(todo)
unzip -o phpunuhi.zip
rm -f phpunuhi.zip
```

### Composer

You can also use PHPUnuhi with Composer. Just install it with this script.

```
composer require boxblinkracer/phpunuhi
```

You can then run it with this command, once you have a configuration.

```
php vendor/bin/phpunuhi validate
```

## Configuration

The whole configuration is done using a XML file.
You can create different translation suites with different files and settings.

Configure a **translation** node for every scope of translation.
This scope can then contain multiple files that need to match and only vary in their content.

```xml

<phpunuhi>
    <translations>

        <translation name="Storefront">
            <file locale="de">./snippets/storefront/de.json</file>
            <file locale="en">./snippets/storefront/en.json</file>
        </translation>

        <translation name="Admin">
            <file locale="de">./snippets/admin/de.json</file>
            <file locale="en">./snippets/admin/en.json</file>
        </translation>

    </translations>
</phpunuhi>
```

## Validate Command

You can then start the validation of your translation files by running this command.

```bash 
php vendor/bin/phpunuhi validate --configuration=./phpunuhi.xml
```

## Export Command

You can easily export your translations into a CSV file that can be passed on to an external translator or company.
Every row will contain the translation key, and every column will be a different translation.

```bash 
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml

# provide custom export folder
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml --dir=.exports

# only export single suite
php vendor/bin/phpunuhi export --configuration=./phpunuhi.xml --suite="my suite"
```

## Import Command

You can easily import your translations from a CSV file.
This will automatically update the JSON files that have been assigned to the imported translation suite.

```bash 
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv

# intent of 4 spaces
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv --intent=4

# sort JSON alphabetically
php vendor/bin/phpunuhi import --configuration=./phpunuhi.xml --suite=storefront --file=./storefront.csv --sort
```
