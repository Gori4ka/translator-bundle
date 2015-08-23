# Translator bundle for symfony2

Translate any text via google, microsoft, yandex translate api.

## Installation

Add to `composer.json`:

```
"develoid/translator-bundle": "dev-master"
```

Add bundle to `app/AppKernel.php`

```php
<?php
// ...

$bundles = array(
    //...
    new Develoid\TranslatorBundle\DeveloidTranslatorBundle()
);
```

Setup to `app/config/config.yml`

```
develoid_translator:
    default: google
    google:
        api_key: %google_translator_api_key%
    yandex:
        api_key: %yandex_translator_api_key%
    microsoft:
        client_id: %microsoft_translator_client_id%
        client_secret: %microsoft_translator_client_secret%
```

## Usage

```php
<?php

// Symfony Controller
// ...

// Google translate api
$translation = $this->get('develoid_translator.google_translator')->translate('text', 'en', 'fr');

// Microfost translate api
$translation = $this->get('develoid_translator.microsoft_translator')->translate('text', 'en', 'fr');
$voice = $this->get('develoid_translator.microsoft_translator')->speak('text', 'en'); //return voice of text

// Yandex translate api
$translation = $this->get('develoid_translator.yandex_translator')->translate('text', 'en', 'fr');
```