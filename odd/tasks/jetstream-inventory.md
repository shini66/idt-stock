# Jetstream inventory integration

Integrate Jetstream Livewire with the existing Laravel inventory controllers while preserving the current inventory domain.

## Specs

S1. "aplicar los cambios de 1 al 5"
S2. "Livewire básico (recomendado)"
S3. "revisa el codigo y dime como implementar laravel Jetstream y usa aplicar los controladores que ya tenemos creado para el control de inventario"

## Tasks

- T1 | S1-S3 | install and restrict Jetstream Livewire to the basic authentication scope | commit: 1035fe3
- T2 | S1-S3 | correct inventory authorization, movement ownership, relationships, and product CRUD defects | commit: 1035fe3
- T3 | S1-S2 | create authenticated inventory and product Blade views | commit: 1035fe3
- T4 | S1-S3 | add focused Pest coverage for access and inventory actions | commit: 1035fe3
- T5 | S1-S3 | format and verify the complete integration | commit: 1035fe3

## Log

- L1 | "revisa el codigo y dime como implementar laravel Jetstream y usa aplicar los controladores que ya tenemos creado para el control de inventario"
- L2 | User selected: "Livewire básico (recomendado)"
- L3 | "aplicar los cambios de 1 al 5"
- L4 | Baseline note: routes/web.php already has an uncommitted correction from `can:manage-products` to `can:manage-product`; preserve it as an existing workspace change.
- L5 | T1 blocked before mutation: `composer show --direct` exited 127 because Composer is unavailable in PATH.
- L6 | T2 implementation changed controllers, models, and focused Pest tests, but `vendor/bin/pest` could not start because `/usr/bin/env: 'php': No such file or directory`; no test was executed.
- L7 | Environment setup authorized and completed: PHP 8.4.27 and Composer 2.11.2 are available through explicit absolute paths; existing process PATH remains stale.
- L8 | T1 installed Jetstream 5.5.3 and Livewire 3.8.11, removed generated API/token artifacts, and verified `/dashboard` redirects authenticated users to `stock.index` (1 Pest test, 2 assertions); no API routes remain.
- L9 | The one authorized focused Pest run for T2 found two 403 failures caused by movement-test users lacking the `operator` role. The fixture was corrected, but the one-run limit prohibits a rerun without a new user request.
- L10 | Jetstream basic scope was completed: email verification remains enabled; API tokens, teams, 2FA, passkeys, their untracked generated migrations, views, and tests were removed. Existing base migrations were not rewritten.
- L11 | User authorized a new bounded verification attempt. Focused Pest passed: 7 tests, 23 assertions. Vite production build passed.
- L12 | Work unit commit 1035fe3 includes the feature. It exceeds the normal review size because Jetstream-generated scaffolding and lockfiles are an indivisible installation boundary; no further commits are planned.
