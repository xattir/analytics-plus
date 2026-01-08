# Performance Optimization Summary

## Overview
This document summarizes the performance optimizations applied to the analytics dashboard to handle millions of rows efficiently.

## Slow Queries Fixed

### 1. Top Pages Query (4.97s → ~500ms expected)
**Original Query:**
```sql
SELECT p.path, SUM(1) as views
FROM analytics_session_paths p
JOIN analytics_sessions s ON s.session_id = p.session_id
WHERE s.site_id = 1 AND s.first_seen BETWEEN ... AND s.is_bot = 0
GROUP BY p.path ORDER BY views DESC LIMIT 30;
```

**Fixes Applied:**
- Added index: `idx_site_bot_first_seen_session` on `(site_id, is_bot, first_seen, session_id)`
- Added index: `idx_site_session_path` on `analytics_session_paths (site_id, session_id, path(191))`
- Changed query to use `first_seen_date` instead of `first_seen` for better index usage

**Expected EXPLAIN:**
- `analytics_sessions`: key=idx_site_bot_first_seen_session, type=ref, rows=actual range
- `analytics_session_paths`: key=idx_site_session_path, type=ref
- No Using temporary; Using filesort

---

### 2. Daily Time Series (2.75s → ~300ms expected)
**Original Query:**
```sql
SELECT DATE(first_seen) as date, SUM(1) as sessions, SUM(pages_count) as pageviews
FROM analytics_sessions
WHERE site_id = 1 AND first_seen BETWEEN ...
GROUP BY DATE(first_seen) ORDER BY date ASC;
```

**Fixes Applied:**
- Added generated column: `first_seen_date DATE GENERATED ALWAYS AS (DATE(first_seen)) STORED`
- Added index: `idx_site_first_seen_date` on `(site_id, first_seen_date)`
- Changed query to use `first_seen_date` directly instead of `DATE(first_seen)`

**Expected EXPLAIN:**
- key=idx_site_first_seen_date, type=range
- No Using temporary; Using filesort (GROUP BY uses index)

---

### 3. Exit Pages (2.33s → ~200ms expected)
**Original Query:**
```sql
SELECT exit_path, SUM(1) as exits
FROM analytics_sessions
WHERE site_id = 1 AND last_seen BETWEEN ...
GROUP BY exit_path ORDER BY exits DESC LIMIT 10;
```

**Fixes Applied:**
- Added generated column: `last_seen_date DATE GENERATED ALWAYS AS (DATE(last_seen)) STORED`
- Added index: `idx_site_last_seen_exit` on `(site_id, last_seen, exit_path(191))`
- Changed query to use `last_seen_date`

**Expected EXPLAIN:**
- key=idx_site_last_seen_exit, type=range

---

### 4. Quality Metrics (1.52s → ~100ms expected)
**Original Query:**
```sql
SELECT
  SUM(CASE WHEN is_bot=0 AND pages_count>1 AND duration_ms>30000 AND max_scroll_percent>50 THEN 1 ELSE 0 END) as high_quality,
  SUM(CASE WHEN is_bot=0 AND (pages_count=1 OR duration_ms<5000 OR max_scroll_percent<10) THEN 1 ELSE 0 END) as low_quality
FROM analytics_sessions WHERE site_id = 1 AND first_seen BETWEEN ...;
```

**Fixes Applied:**
- Added precomputed flags: `is_high_quality`, `is_low_quality` (TINYINT)
- Added index: `idx_site_date_quality` on `(site_id, first_seen_date, is_bot, is_high_quality, is_low_quality)`
- Updated `AnalyticsController` to compute flags at insert/update time
- Changed query to: `SUM(is_high_quality = 1)`, `SUM(is_low_quality = 1)`

**Expected EXPLAIN:**
- key=idx_site_date_quality, type=range
- Much faster than CASE expressions

---

### 5. Daily Distinct Visitors (1.84s → ~200ms expected)
**Original Query:**
```sql
SELECT DATE(first_seen) as date, COUNT(DISTINCT device_fingerprint) as count
FROM analytics_sessions
WHERE site_id=1 AND first_seen BETWEEN ... AND is_bot=0
GROUP BY DATE(first_seen) ORDER BY date ASC;
```

**Fixes Applied:**
- Added covering index: `idx_site_date_bot_fingerprint` on `(site_id, first_seen_date, is_bot, device_fingerprint)`
- Changed query to use `first_seen_date` instead of `DATE(first_seen)`

**Expected EXPLAIN:**
- key=idx_site_date_bot_fingerprint, type=range, Using index (index-only scan)

---

### 6. Country Breakdown (2.83s → ~300ms expected)
**Original Query:**
```sql
SELECT country, SUM(1) as count
FROM analytics_sessions
WHERE site_id=1 AND first_seen BETWEEN ... AND is_bot=0 AND country IS NOT NULL
GROUP BY country ORDER BY count DESC LIMIT 10;
```

**Fixes Applied:**
- Added index: `idx_site_bot_first_seen_country` on `(site_id, is_bot, first_seen, country)`
- Changed query to use `first_seen_date`

**Expected EXPLAIN:**
- key=idx_site_bot_first_seen_country, type=range

---

## Schema Changes

### Generated Columns
1. `first_seen_date DATE GENERATED ALWAYS AS (DATE(first_seen)) STORED`
2. `last_seen_date DATE GENERATED ALWAYS AS (DATE(last_seen)) STORED`

### Precomputed Flags
1. `is_high_quality TINYINT DEFAULT 0` - Computed at insert: `is_bot=0 AND pages_count>1 AND duration_ms>30000 AND max_scroll_percent>50`
2. `is_low_quality TINYINT DEFAULT 0` - Computed at insert: `is_bot=0 AND (pages_count=1 OR duration_ms<5000 OR max_scroll_percent<10)`

### Indexes Added

#### analytics_sessions
- `idx_site_first_seen_core` - `(site_id, first_seen)`
- `idx_site_last_seen_core` - `(site_id, last_seen)`
- `idx_site_bot_first_seen_core` - `(site_id, is_bot, first_seen)`
- `idx_site_bot_last_seen_core` - `(site_id, is_bot, last_seen)`
- `idx_site_bot_first_seen_session` - `(site_id, is_bot, first_seen, session_id)` - Covering for JOINs
- `idx_site_first_seen_date` - `(site_id, first_seen_date)` - For GROUP BY date
- `idx_site_bot_first_seen_date` - `(site_id, is_bot, first_seen_date)`
- `idx_site_date_bot_fingerprint` - `(site_id, first_seen_date, is_bot, device_fingerprint)` - Covering for DISTINCT
- `idx_site_date_quality` - `(site_id, first_seen_date, is_bot, is_high_quality, is_low_quality)`
- `idx_site_date_session_covering` - `(site_id, first_seen_date, session_id)`
- `idx_site_bot_first_seen_country` - `(site_id, is_bot, first_seen, country)`
- `idx_site_bot_first_seen_browser` - `(site_id, is_bot, first_seen, browser)`
- `idx_site_first_seen_device` - `(site_id, first_seen, device_type)`
- `idx_site_first_seen_os` - `(site_id, first_seen, os)`
- `idx_site_first_seen_entry` - `(site_id, first_seen, entry_path(191))`
- `idx_site_last_seen_exit` - `(site_id, last_seen, exit_path(191))`
- `idx_site_bot_first_seen_referrer` - `(site_id, is_bot, first_seen, referrer_source)`

#### analytics_session_paths
- `idx_site_session_path` - `(site_id, session_id, path(191))` - Critical for JOIN queries
- `idx_site_path` - `(site_id, path(191))`

---

## Code Changes

### BackendAnalyticsController
- All queries updated to use `first_seen_date` / `last_seen_date` instead of `DATE(first_seen)` / `DATE(last_seen)`
- Quality metrics query uses `SUM(is_high_quality = 1)` instead of CASE expressions
- All breakdown queries (country, browser, device, OS, etc.) use `first_seen_date` for better index usage

### AnalyticsController (API)
- Added logic to compute `is_high_quality` and `is_low_quality` at insert/update time
- Flags are set based on: pages_count, duration_ms, max_scroll_percent, is_bot

### AnalyticsSession Model
- Added `is_high_quality`, `is_low_quality` to `$fillable`
- Added casts for quality flags and date columns

---

## Migration Files

1. `2026_01_08_000001_add_generated_date_and_quality_flags.php`
   - Adds generated columns and quality flags
   - Adds initial critical indexes

2. `2026_01_08_000002_backfill_quality_flags_and_date.php`
   - Backfills quality flags for existing rows (chunked, safe for large tables)

3. `2026_01_08_000003_add_critical_performance_indexes.php`
   - Adds all remaining performance indexes
   - Uses `ALGORITHM=INPLACE, LOCK=NONE` for online schema changes

---

## Expected Performance Improvements

| Query | Before | After (Expected) | Improvement |
|-------|--------|------------------|-------------|
| Top Pages | 4.97s | ~500ms | **90% faster** |
| Daily Time Series | 2.75s | ~300ms | **89% faster** |
| Exit Pages | 2.33s | ~200ms | **91% faster** |
| Quality Metrics | 1.52s | ~100ms | **93% faster** |
| Daily Distinct Visitors | 1.84s | ~200ms | **89% faster** |
| Country Breakdown | 2.83s | ~300ms | **89% faster** |

**Total Dashboard Load Time: ~30s → ~2-3s expected (90% improvement)**

---

## Safety Considerations

1. **Online Schema Changes**: All indexes use `ALGORITHM=INPLACE, LOCK=NONE` to minimize downtime
2. **Chunked Backfill**: Quality flags are backfilled in chunks of 10,000 rows with delays
3. **Index Existence Checks**: All migrations check for existing indexes/columns before creating
4. **Generated Columns**: Automatically populated, no manual updates needed

---

## Next Steps

1. Run migrations in order:
   ```bash
   php artisan migrate --path=database/migrations/2026_01_08_000001_add_generated_date_and_quality_flags.php
   php artisan migrate --path=database/migrations/2026_01_08_000002_backfill_quality_flags_and_date.php
   php artisan migrate --path=database/migrations/2026_01_08_000003_add_critical_performance_indexes.php
   ```

2. Monitor query performance using Laravel Debugbar or MySQL slow query log

3. Verify EXPLAIN plans show correct index usage

4. For extremely large datasets (100M+ rows), consider:
   - Partitioning by date
   - Daily rollup tables for historical data
   - Caching for older, less frequently changing data

