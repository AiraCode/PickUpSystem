# Translation System Migration Notes

## Previous Architecture Description
The application formerly relied on a manual `data-i18n` attribute system to manage translations. Every text node that required translation had to be wrapped in an HTML tag featuring a unique translation key (e.g. `<span data-i18n="nav.dashboard">Dashboard</span>`). 
The dictionary definitions were separated into `id.json` and `en.json`. The active language was mapped dynamically using JavaScript modules `i18n.js` and `i18n-observer.js`.

## Removal Rationale
This approach caused significant maintenance friction:
- Developers were required to constantly invent and manage hundreds of arbitrary keys (e.g. `auth.login`, `btn.save`).
- Every new page required tedious modifications to Blade templates to inject `data-i18n`.
- Dynamic content generated from JS logic required complicated wrapper elements.

## New Mapping Strategy (August 2026)
The application has successfully migrated to a **Global Automatic Dictionary-Based Translation System**.

**How it works:**
1. A single, unified `dictionary.json` maps complete Indonesian phrases directly to their desired English translations, structured to support future locales (e.g. `{"Konfirmasi": {"en": "Confirm"}}`).
2. An invisible `page-translator.js` engine (utilizing `TreeWalker` and `MutationObserver`) traverses all text nodes and updates the UI instantly, completely offline.
3. Original text nodes are preserved securely in memory via `WeakMap`, ensuring zero DOM bloat and instantaneous rollback without cumulative translation errors.
4. Developers no longer write translation keys; they simply write standard Indonesian text. Any missing translations are automatically logged in the console.

*Note: The original `id.json`, `en.json`, `i18n.js`, and `i18n-observer.js` files were fully deprecated and deleted from the codebase during this migration. They are not replicated here to avoid misleading versions.*
