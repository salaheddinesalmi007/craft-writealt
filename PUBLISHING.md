# Craft CMS Marketplace publishing

## Test locally

1. Put this plugin in a public Git repository, or use a local Composer repository while testing.
2. From a Craft 5 project, run `composer require writealt/craft-writealt`.
3. Run `php craft plugin/install writealt`.
4. Open **Settings > Plugins > WriteAlt - AI Alt Text & SEO** and save a WriteAlt API key.
5. Open **WriteAlt** in the control panel and test generation, filters, bulk selection, and optimization on a disposable image volume.
6. Confirm that generation changes only the Asset alt field and that optimization keeps the same Asset ID and filename.

## Submit to the Craft Plugin Store

1. The public repository is https://github.com/salaheddinesalmi007/craft-writealt. Keep `composer.json`, `README.md`, `CHANGELOG.md`, `LICENSE.txt`, `icon.svg`, and the plugin source at the repository root.
2. Create or sign in to a Craft Console account and connect the GitHub account that owns the repository.
3. In Craft Console, choose **Plugins**, add the repository, and complete the plugin profile. Use the name **WriteAlt - AI Alt Text & SEO**, the handle `writealt`, the homepage `https://writealt.com`, the documentation URL `https://writealt.com/documentation`, and the support email `support@writealt.com`.
4. Provide the Marketplace description from `README.md`, the MIT license, the icon, and the current changelog.
5. Submit the plugin for approval. Craft reviews the listing and package before it is published.
6. After approval, create a semantic Git tag such as `1.0.0` and push it. Craft uses the tagged release for the Marketplace package.

## Listing description

WriteAlt brings AI-assisted alt text and in-place image optimization to the Craft CMS control panel. Browse image assets, filter missing descriptions or large files, generate text in 138 languages, and save it directly to Craft's native Asset alt field. Optimize supported images through Craft's Asset service without creating duplicate assets, while preserving existing alt text and asset references. The API key remains in Craft's server-side plugin settings.
