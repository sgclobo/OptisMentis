# Update Report

Date: 2026-05-13
Project: OptisMentis / hypnosis-app

## Latest Changes

### 1. Translation Coverage Expanded Across the App

The app was updated so page content (not only navbar labels) now respects the selected language. Translation keys and template usage were expanded across:

- Public pages (home, blog, services, appointment, intake, auth pages)
- Client area pages
- Admin area pages

New translation groups include:

- `common.*`
- `home.*`
- `blog.*`
- `appointment.*`
- `intake.*`
- `client.*`
- `admin.*`

Locales currently supported:

- `en`
- `pt`
- `tet`
- `id`

### 2. Translation Lookup Reliability

Language selection and translation fallback are handled through existing helper functions:

- Locale from session/query (`lang`)
- Key lookup with fallback to default locale (`en`)

### 3. Database Bootstrap Hardening

Database bootstrap was updated to be more resilient:

- Supports environment values from `getenv`, `$_ENV`, and `$_SERVER`
- Supports local override config files
- Improved error logging for config load and connection failures
- Avoids immediate hard crash in some failure scenarios

### 4. Fatal Error Fix (`t()` undefined)

A bootstrap ordering issue was fixed so translation helpers are loaded before templates call `t()`. This resolved:

- `Fatal error: Call to undefined function t()`

### 5. Git Ignore Setup

Root ignore rules were added to avoid committing environment-sensitive DB config files.

## How To Adjust Language Packages

All language strings are defined in:

- `hypnosis-app/config/translations.php`

Each locale has its own dictionary array. To adjust existing language text:

1. Open `hypnosis-app/config/translations.php`.
2. Find the locale block you want to edit (`en`, `pt`, `tet`, `id`).
3. Find the target key (example: `home.hero_title`).
4. Update only the value string, keeping the same key.
5. Repeat the same key across all locale blocks to keep parity.

Example pattern:

```php
'home.hero_title' => 'Transform your mind, restore your calm',
```

## Rules For Adding New Translations In New Files

When creating a new PHP page/template, follow this process.

### A. Use Translation Helpers, Not Hardcoded UI Text

Do not hardcode user-facing English text directly in templates.

Use:

```php
<?= e(t('your.key.here')) ?>
```

with placeholders when needed:

```php
<?= e(t('dashboard.welcome', ['name' => $userName])) ?>
```

### B. Create Keys First

Before or while building the new page:

1. Define a prefix namespace for the new feature/page:
   - Example: `payments.*`, `onboarding.*`, `reports.*`
2. Add all required keys under `en` in `translations.php`.
3. Add the same keys under `pt`, `tet`, and `id`.

Important: never add keys to only one locale.

### C. Suggested Key Naming Convention

Use stable, descriptive dot notation:

- `section.page_title`
- `section.heading`
- `section.intro`
- `section.empty`
- `section.error_*`
- `section.success_*`
- `section.button_*`
- `section.label_*`

Examples:

- `reports.page_title`
- `reports.heading`
- `reports.empty`
- `reports.error_invalid_date`

### D. Keep Dynamic/Data Values Separate

Translate UI labels/messages, not database content unless explicitly designed for multilingual content storage.

Good:

- Translate `"Status"`, `"Save"`, `"No records found"`

Not automatic:

- Free-form blog post body stored in DB

### E. Add Fallback-Safe Keys

If a key is missing in a non-default locale, fallback goes to `en` key value. Still, every new key should be added to all locales during development to avoid mixed-language UI.

### F. Validate Before Deploy

For each new file/feature:

1. Switch languages via `?lang=en|pt|tet|id`.
2. Verify labels, headings, buttons, alerts, and empty states.
3. Confirm no raw key strings appear in UI.
4. Run PHP lint checks on modified files.

## Recommended Workflow For Future Features

1. Build UI structure with translation keys only.
2. Add/verify keys in `translations.php` for all locales.
3. Wire placeholders for dynamic text.
4. Test all locales.
5. Deploy.

This keeps language behavior consistent and prevents regressions where only part of the UI is translated.
