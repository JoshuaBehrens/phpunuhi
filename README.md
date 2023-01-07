<p align="center">
   <img src="/.github/assets/home-logo.png">
</p>

![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/ci_pipe.yml/badge.svg)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/boxblinkracer/phpunuhi)
![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/boxblinkracer/phpunuhi/latest)
![Build Status](https://github.com/boxblinkracer/phpunuhi/actions/workflows/nightly_build.yml/badge.svg)

Welcome to PHPUnuhi - The easy framework to validate and manage translation files!

Unuhi? This is Hawaiian for "translate" or "translation".
Now that you know this, let's get started!

<!-- TOC -->

* [1. Basic Concept](#1-basic-concept)
* [2. Installation](#2-installation)
* [3. Configuration](#3-configuration)
* [4. Validate Command](#4-validate-command)
    * [5. Validations](#5-validations)
        * [5.1 Invalid structure](#51-invalid-structure)
        * [5.2 Missing translations](#52-missing-translations)
* [6. Export Command](#6-export-command)
* [7. Import Command](#7-import-command)
* [8. Translate Command](#8-translate-command)
* [9. Use Cases](#9-use-cases)
    * [9.1 Validation in CI pipeline](#91-validation-in-ci-pipeline)
    * [9.2 Working with external translation agencies](#92-working-with-external-translation-agencies)
    * [9.3 Live WebEdit with HTML](#93-live-webedit-with-html)
    * [9.4 Automatic Translation with DeepL](#94-automatic-translation-with-deepl)
* [10 Appendix](#10-appendix)
    * [10.1 Storage Formats](#101-storage-formats)
        * [10.1.1 JSON](#1011-json)
    * [10.2 Exchange Formats](#102-exchange-formats)
        * [10.2.1 CSV](#1021-csv)
        * [10.2.2 HTML / WebEdit](#1022-html--webedit)
    * [10.3 Translator Services](#103-translator-services)
        * [10.3.1 DeepL](#1031-deepl)
        * [10.3.2 Google Cloud Translate](#1032-google-cloud-translate)

<!-- TOC -->

## 1. Basic Concept

This is a framework that helps you to **validate and maintain translation files**.
At the moment it only supports JSON based files.

Although it's not dependent on a specific platform, you can use it perfectly with Shopware 6 and other platforms.

For example, Shopware 6 has snippets based on JSON files.
If you develop plugins for this platform, you can build translation sets in PHPUnuhi that contain all your files for the individual languages, like EN, DE, NL, and whatever you support.
PHPUnuhi helps you to make sure you didn't forget any translations, screwed up structures across your language files and even
helps you to export and import your translations.

One of the benefits is, that this framework does not require anything else than your translation files.
This makes it a perfect fit for your CI/CD pipelines.

**Key Benefits**

* Validates structure and content
* Platform independent with option for different storage formats
* Exchange formats such as CSV and HTML
* Live WebEdit with HTML exchange format
* Automatic translation using DeepL (or more)

<p align="center">
   <img src="/.github/assets/works-with.jpg">
</p>


> Missing your platform or file format? Feel free to contribute :)

## 2. Installation

You can use PHPUnuhi with Composer. Just install it with this script.

```
composer require boxblinkracer/phpunuhi
```

You can then run it with this command, once you have a configuration.

```
php vendor/bin/phpunuhi validate
```

## 3. Configuration

The whole configuration is done using XML.
You can create different **translation sets** with different files and settings.

Configure a **translation set** for every bundle (scope) of your translations.
Such a set can then contain multiple files with your individual language and locale values.

Create a new **phpunuhi.xml** file (or rename it to something else).

```xml

<phpunuhi>
    <translations>

        <set name="Storefront">
            <file locale="de">./snippets/storefront/de.json</file>
            <file locale="en">./snippets/storefront/en.json</file>
        </set>

        <set name="Admin" format="json" jsonIntent="4" jsonSort="true">
            <file locale="de">./snippets/admin/de.json</file>
            <file locale="en">./snippets/admin/en.json</file>
        </set>

    </translations>
</phpunuhi>
```

* Every set can have its own storage format (default is JSON).
* A JSON set can have 2 additional attributes
    * jsonIntent [optioanl]: the intent for the imported and formatted JSON files (e.g. jsonIntent="3"). default is "2"
    * jsonSort [optioanl]: indicates if the JSON data should be sorted alphabetically or not (e.g. jsonSort="true"), default is "false"
* Every translation file needs its own locale.

## 4. Validate Command

Start the validation of your translation files by running this command:

```bash 
# loads configuration phpunuhi.xml as default
php vendor/bin/phpunuhi validate 

# provide custom configuration
php vendor/bin/phpunuhi validate --configuration=./translations.xml
```

### 5. Validations

#### 5.1 Invalid structure

The command will check if all files of a translation set have the **same structure**.
If not, you might have forgotten something ;)

<p align="center">
   <img src="/.github/assets/validation-structure.png">
</p>

#### 5.2 Missing translations

If missing translations (**empty values**) are found, the validation process will fail.
This helps against forgetting certain translations in any of your files.

<p align="center">
   <img src="/.github/assets/validation-empty.png">
</p>

## 6. Export Command

You can export your translations **into a CSV file**, a HTML WebEdit spreadsheet, or other supported exchange formats.
These files can then be passed on to an external translator or company.

Every row will contain the translation key, and every column in that row will be a different translation (in case of CSV files).

```bash 
# default export in default exchange format CSV
php vendor/bin/phpunuhi export 

# default export in specific exchange format
php vendor/bin/phpunuhi export ... --format=html

# provide custom export folder
php vendor/bin/phpunuhi export ... --dir=./exports

# only export single set "storefront"
php vendor/bin/phpunuhi export ... --set="storefront"
```

> For more options and arguments of the formats please see the appendix below!

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

## 7. Import Command

You can import your translations **from a CSV file** or other supported exchange formats.
This will automatically update the storage files (JSON, ...) that have been assigned to the imported translation set.

> It's recommended to use version control to verify changes, before approving them.

```bash 
# import from default format CSV
php vendor/bin/phpunuhi import --set=storefront --file=storefront.csv

# import with other exchange format
php vendor/bin/phpunuhi import ... --format=html
```

## 8. Translate Command

PHPUnuhi includes the option to use external service to automatically translate missing values for you.
The command will search for empty values in your translation sets.

If an empty translation is found, it will try to find an existing one from a different locale.
This one is then used as source locale for the external translation service.

After all values are filled, your storage is updated and saved.

```bash 
php vendor/bin/phpunuhi translate --service=abc

# translate using DeepL
php vendor/bin/phpunuhi translate ... --service=deepls --deepl-key=xyz
```

> For more options and arguments of the formats please see the appendix below!

## 9. Use Cases

Here are a few use cases and ideas to get you started.

### 9.1 Validation in CI pipeline

One of the typical things you want to make sure is, that your plugin/software doesn't miss any
required translations.

This can be done directly within your CI pipeline. Just install your dependencies and run the validation command.
The exit value of this command will automatically stop your pipeline if an error is detected.

### 9.2 Working with external translation agencies

External translation agencies often require CSV exports.
You can easily generate and export a CSV file for your partner agencies.

Once they have adjusted their translation, they can send you the file back and you simply
import it again with the import command.

### 9.3 Live WebEdit with HTML

If you have a test or staging system, you can even go one step further.
Just imagine setting up a cronjob that runs after a deployment, or as scheduled job.
This cronjob could trigger the HTML export of PHPUnuhi with an output directory to a folder that is available within your DocRoot.
That HTML file might then be exposed with something like this **https://stage.my-shop.com/snippets**.

Everyone who wants to either see all translations, or even modify them, can easily do this in their browser.
And because you use a cronjob to generate the file, it's always automatically updated.

### 9.4 Automatic Translation with DeepL

If you have a DeepL API key, you can automatically have PHPUnuhi translate missing texts using this services (or another supported service).
In this case you might only want to translate 1 single language, and have DeepL doing the rest for you.

> A double check and approval in your version control would still be a good thing.

## 10 Appendix

### 10.1 Storage Formats

Storage formats define how your translations are stored.
Every format has its own loading and saving implementation.

The following formats are currently supported.

#### 10.1.1 JSON

* Format: "json"
* XML Set Attributes:
    * --json-sort
    * --json-intent

The JSON format means that your files are stored in separate JSON files.
Every locale has its own JSON file.
The JSON structure across all files of a set should match.

### 10.2 Exchange Formats

Exchange formats define how you export and import translation data.
The main purpose is to send it out to a translation company or just someone else,
and be able to import it back into your system again.

The following formats are currently supported.

#### 10.2.1 CSV

* Format: "csv"
* Arguments:
    * Export Command
        * --csv-delimiter=","
    * Import Command
        * --csv-delimiter=","

The CSV format is a well known and solid format for interoperability.
You can open CSV files with Microsoft Excel, Apple Numbers as well as simple text editors or more.
The only downside with Excel and Numbers is, that they might force you to save the updated file in their own formats (just pay attention to this).

The benefit is that you can simply open all translation in a spreadsheet.
Every translation key has its own row, and all locale-values have their own column in that row.

<p align="center">
   <img src="/.github/assets/csv.png">
</p>

#### 10.2.2 HTML / WebEdit

* Format: "html"

The HTML export helps you to export all translations into a single HTML file.
You can then open this file in your browser and immediately start to edit your translations.

Once finished, just click on "save translations". This will download a **html.txt** file that
you can import again into your system with the format **html** in PHPUnuhi.

<p align="center">
   <img src="/.github/assets/html.png">
</p>

### 10.3 Translator Services

Translators are supported (external) services that automatically translate empty values for you.
These services usually require an API key that needs to be provided for PHPUnuhi.

#### 10.3.1 DeepL

* Service: "deepl"
* Arguments:
    * Translate Command
        * --deepl-key=xyz
        * --deepl-formal

DeepL is one of the leading translation services.
If you have an API Key from DeepL, you can send your translation requests directly to their API.

DeepL allows you to either translate to a formal or informal language.
This option is only available for some target languages, just like "German" ("du" vs. "Sie").
You can request a formal language by simply applying the argument "--deepl-formal" to the translate command.

#### 10.3.2 Google Cloud Translate

* Service: "google"
* Arguments:
    * Translate Command
        * --google-key=xyz

Google Cloud Translation allows you to use the AI services of Google.
If you have an API Key, you can easily provide it with the corresponding argument when running the translation command.

