# Changelog

## 1.0.9 - 2026-09-29

- Removed the blocked documentation URL from the package metadata used by Craft Console.

## 1.0.8 - 2026-09-29

- Moved asset status labels into the white card content area.
- Repackaged the supplied WriteAlt SVG wordmark with the plugin.

## 1.0.7 - 2026-09-29

- Fixed generation parsing for the API's `data.alt_texts` response.
- Registered the native asset action on Craft asset edit hooks.
- Styled generation errors as contained dashboard alerts.

## 1.0.6 - 2026-09-29

- Added a native Generate alt text button to Craft asset edit screens.
- Added a data preview fallback for private or non-public asset volumes.
- Applied the supplied WriteAlt wordmark to the dashboard.
- Clarified the missing-alt-text state on each asset card.

## 1.0.5 - 2026-09-29

- Added a Select all visible control for bulk generation and optimization.

## 1.0.4 - 2026-09-29

- Restored Craft thumbnail previews when an asset has no public URL.
- Added the full WriteAlt logo beside the dashboard title.
- Removed the plugin SVG icon from Craft’s left navigation.

## 1.0.3 - 2026-09-29

- Added Craft-compatible full-color and control-panel navigation icons.

## 1.0.2 - 2026-09-29

- Placed control-panel templates under the Composer source root so Craft can resolve them correctly.

## 1.0.1 - 2026-09-29

- Registered the WriteAlt control-panel route explicitly for Craft 5.
- Corrected the dashboard asset bundle path.

## 1.0.0 - 2026-09-29

- Added a native Craft CMS control-panel section for image assets.
- Added missing-alt-text generation in 138 languages and five writing styles.
- Added in-place image optimization that preserves the Craft asset ID and alt text.
- Added server-side API-key settings, credit status, filters, bulk selection, and per-asset feedback.
