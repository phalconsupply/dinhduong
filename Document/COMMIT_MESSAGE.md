## 🎯 Commit: Add Auto-Switching System for Z-Score Methods

### Summary
Added flexible auto-switching system allowing administrators to dynamically choose between WHO LMS and SD Bands methods via database settings, without code deployment.

### Changes

#### 1. Database Migration
**File**: `database/migrations/2025_11_04_180122_add_zscore_method_setting.php`
- Added `zscore_method` setting to settings table
- Default value: `'lms'` (WHO LMS 2006)
- Allowed values: `'lms'` or `'sd_bands'`
- Rollback support via migration down method

#### 2. Helper Functions
**File**: `app/Helpers/common.php`
- `getZScoreMethod()`: Returns current method from settings ('lms' or 'sd_bands')
- `isUsingLMS()`: Boolean check if LMS is active

#### 3. History Model Auto Methods
**File**: `app/Models/History.php`
Added 8 wrapper methods that auto-select implementation based on setting:

**Z-Score Methods:**
- `getWeightForAgeZScoreAuto()`
- `getHeightForAgeZScoreAuto()`
- `getBMIForAgeZScoreAuto()`
- `getWeightForHeightZScoreAuto()`

**Classification Methods:**
- `check_weight_for_age_auto()`
- `check_height_for_age_auto()`
- `check_bmi_for_age_auto()`
- `check_weight_for_height_auto()`

Each method:
```php
public function methodNameAuto() {
    return isUsingLMS() 
        ? $this->methodNameLMS() 
        : $this->methodNameOld();
}
```

#### 4. Comparison Tool
**File**: `app/Console/Commands/CompareZScoreMethods.php`
- Command: `php artisan who:compare-methods`
- Options:
  * `--limit=N`: Number of records to compare (default: 100)
  * `--export`: Export results to CSV
- Features:
  * Compares Z-scores from both methods
  * Calculates mean/max/min differences
  * Counts classification changes
  * Overall assessment (EXCELLENT/GOOD/REVIEW REQUIRED)
  * CSV export for detailed analysis

#### 5. Documentation
**New Files:**
- `ZSCORE_METHOD_SETTINGS.md`: Complete settings guide
- `LMS_IMPLEMENTATION_SUMMARY.md`: Full implementation overview
- Updated `HUONG_DAN_UPDATE_DASHBOARD_LMS.md`: Recommends auto methods

### Benefits

✅ **Flexibility**: Switch methods via SQL, no deployment needed
✅ **Rollback Safety**: Instant rollback if issues found with LMS
✅ **A/B Testing**: Easy comparison between methods
✅ **Future-Proof**: Ready for future WHO standard updates
✅ **Backward Compatible**: All old methods still work

### Usage Example

```php
// In Controllers/Views
$history = History::find($id);

// Auto-select based on admin setting
$zscore = $history->getWeightForAgeZScoreAuto();
$result = $history->check_weight_for_age_auto();
```

```sql
-- Switch methods via SQL
UPDATE settings SET value = 'lms' WHERE `key` = 'zscore_method';
-- or
UPDATE settings SET value = 'sd_bands' WHERE `key` = 'zscore_method';
```

```bash
# Compare methods
php artisan who:compare-methods --limit=100

# Export comparison
php artisan who:compare-methods --limit=1000 --export
```

### Testing

Run comparison to validate accuracy:
```bash
php artisan migrate
php artisan who:compare-methods --limit=100
```

Expected results:
- Mean difference < 0.05
- Classification changes < 5%
- Overall assessment: EXCELLENT

### Next Steps

1. Run migration: `php artisan migrate`
2. Test comparison command
3. Update DashboardController to use `*_auto()` methods
4. Test dashboard with both settings
5. Set production to LMS method

### Rollback Plan

If any issues:
```sql
UPDATE settings SET value = 'sd_bands' WHERE `key` = 'zscore_method';
```

All calculations will instantly revert to old method.

### Risk Assessment

**Risk Level**: LOW
- Old methods unchanged (backward compatible)
- Database setting allows instant rollback
- Extensive testing tools provided
- No breaking changes to existing code

---

**Commit Type**: Feature
**Impact**: High (enables flexible method switching)
**Breaking Changes**: None
**Migration Required**: Yes
**Testing Required**: Yes (comparison command)
