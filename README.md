# Translator bundle for symfony2

Translate any text via google, microsoft and yandex translate api.

## Installation

Add to `composer.json`:

```
"develoid/translator-bundle": "dev-master"
```

Add bundle to `app/AppKernel.php`:

```php
<?php
// ...

$bundles = array(
    //...
    new Develoid\TranslatorBundle\DeveloidTranslatorBundle()
);
```

Setup to `app/config/config.yml`

```yaml
develoid_translator:
    default: google
    google:
        api_key: %google_translator_api_key%
    yandex:
        api_key: %yandex_translator_api_key%
    microsoft:
        api_key: %microsoft_api_key%
```

## Usage

#### Translate text

```php
<?php

// Symfony Controller
// ...

// Translate text via Google
$translation = $this->get('develoid_translator.google_translator')->translate('text', 'en', 'fr');

// Translate text via Microfost
$translation = $this->get('develoid_translator.microsoft_translator')->translate('text', 'en', 'fr');

// Translate text via Yandex
$translation = $this->get('develoid_translator.yandex_translator')->translate('text', 'en', 'fr');
```

#### Get voice of text

**Google and Yandex doesn't support speak method**

Get voice via Microsoft translator:

```php
<?php

// raw voice
$voice = $this->get('develoid_translator.microsoft_translator')->speak('text', 'en');

file_put_contents('voice.mp3', $voice);
```

#### Detect language

```php
<?php

// Detect language via Google
$language = $this->get('develoid_translator.google_translator')->detect('text');

// Detect language via Microfost
$language = $this->get('develoid_translator.microsoft_translator')->detect('text');

// Detect language via Yandex
$language = $this->get('develoid_translator.yandex_translator')->detect('text');
```
