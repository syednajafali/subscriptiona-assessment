# Refactor, Trial Feature, RBAC & Performance — Implementation Notes

**Date:** 2025-08-28

## What Changed

### Refactor
- Moved business logic into `modules/subscription/services/SubscriptionService.php`.
- Replaced raw SQL with parameterized AR queries (`Subscription::find()->...`) and added `findActiveByUser()`.
- Added `TimestampBehavior` to `Subscription` for automatic `created_at`/`updated_at`.
- Introduced relations `getUser()` and `getPlan()` and used eager loading in controller.
- Removed embedded SQL from the `index` view (kept HTML output identical).

### Trial Subscription Feature
- Implemented a console command `trial/convert` that converts expired trials to paid and queues `SendSubscriptionEmailJob`.
- All conversion logic lives in `SubscriptionService::convertExpiredTrialsToPaid()` to keep controllers thin.

### RBAC-Based Access Control
- Added `AccessControl` to `SubscriptionController`.
- New permissions:
  - `subscription.viewAny` — allows viewing all subscriptions (e.g., admin).
  - `subscription.cancel` — allows cancelling any subscription.
- Owner checks enforced in `view` and `cancel` actions.


### Performance (N+1 Removed)
- Controller uses `with(['user','plan'])` to eager-load relations.
- `index` view remains unchanged visually; query count now stable regardless of row count.

## How to Run

1. Merge `config/web.php` and `config/console.php` from this package into your project (ensure `queue` and `authManager` configured).
2. Apply migrations:
   ```bash
   php yii migrate --migrationPath=@app/migrations
   ```
3. (Optional) Seed roles/users as needed to assign `admin` role.
4. Run the queue worker (for queued emails):
   ```bash
   php yii queue/listen
   ```
5. Convert trials:
   ```bash
   php yii trial/convert
   ```

## Tests
- Placeholders created:
  - `tests/unit/SubscriptionTrialTest.php`
  - `tests/unit/AccessControlTest.php`
  - `tests/functional/SubscriptionIndexCest.php`
Add fixtures and complete assertions in your environment.

## Security
- RBAC rules + owner checks prevent data leakage.
- Parameterized queries via AR prevent SQL injection.

## Backward Compatibility
- `view` action returns the same array keys (`$row[...]`) the legacy view expects.
- No view templates were changed (except removing inline SQL in index).

