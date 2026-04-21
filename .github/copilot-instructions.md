# Copilot Instructions

## Testing

- Do not create new test files.
- Do not modify existing tests.
- Do not run the test suite (`php artisan test`, `vendor/bin/phpunit`, etc.).
- Skip the "write a test or update an existing test" enforcement from `AGENTS.md` for this project until further notice.

## UX for non-technical users

This is a hard rule. The full guidelines live in `AGENTS.md` under `=== ux/non-tech-user rules ===`. Read them before touching any admin form, validation message, toast or label. Highlights:

- Never use a raw text input for a value that comes from a finite set — use `<x-ui.select :options="...">` and centralise the option list in `App\Core\Support\Choices`.
- Forbidden words in any user-facing label, placeholder, hint or error: `PHP date()`, `ISO 4217`, `ISO 639-1`, `ISO 3166`, `snake_case`, `kebab-case`, `regex`, raw HTTP header names, raw route names, dotted config keys, DB column names.
- Every error/toast/validation message must tell the user how to fix the problem in plain English.
- For format-style fields (date, time, currency) show a live preview inside the option label.
- Auto-generate slugs/keys/internal identifiers from the human label; keep the technical field, if any, behind an "Advanced" disclosure and re-label it ("Internal name").
