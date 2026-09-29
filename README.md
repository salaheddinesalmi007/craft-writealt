# WriteAlt for Craft CMS

WriteAlt is a native Craft CMS plugin for generating alt text and optimizing image assets from the Craft control panel.

## Features

- Lists image assets from the current Craft site and volume.
- Shows current alt text, image size, and clear missing/optimization statuses.
- Generates or regenerates alt text for one asset or selected assets.
- Supports 138 languages, five writing styles, and optional SEO keywords.
- Saves generated text to Craft's native Asset `alt` field.
- Optimizes images in place through Craft's Asset service without creating a duplicate asset.
- Keeps the WriteAlt API key in Craft plugin settings on the server.
- Shows the current WriteAlt credit balance and actionable errors.

## Requirements

- Craft CMS 5.0 or newer.
- PHP 8.2 or newer, as required by the selected Craft version.
- A WriteAlt API key.
- GD with JPEG, PNG, and/or WebP support for image optimization.

## Install

From the Craft project directory:

```bash
composer require writealt/craft-writealt
php craft plugin/install writealt
```

Open **Settings > Plugins > WriteAlt - AI Alt Text & SEO**, save the API key and choose the default language, style, keywords, and optimization threshold. Then open **WriteAlt** in the control-panel navigation.

## Asset behavior

Generated text is saved to the selected Craft Asset's native `alt` field. Optimization replaces the file on the same Asset record, keeping its ID, filename, references, and existing alt text. If the optimized file is not smaller, the original remains unchanged.

## Support

Documentation: https://writealt.com/documentation

Support: support@writealt.com
