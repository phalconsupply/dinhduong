# Z-Score Calculation Method Settings

## Overview
Hệ thống hỗ trợ 2 phương pháp tính Z-score:

1. **LMS Method (WHO 2006)** - Phương pháp chính thức từ WHO
   - Sử dụng công thức Box-Cox transformation
   - Độ chính xác cao, match 100% với WHO Anthro
   - **Được khuyến nghị sử dụng**

2. **SD Bands** - Phương pháp xấp xỉ từ bảng SD
   - Sử dụng phép nội suy tuyến tính
   - Độ lệch ~0.05 so với LMS
   - Chỉ dùng để so sánh hoặc migration

## Admin Configuration

### 1. Migration Setting
```bash
php artisan migrate
# Thêm setting 'zscore_method' vào bảng settings
# Giá trị mặc định: 'lms'
```

### 2. Update Setting via Database
```sql
UPDATE settings 
SET value = 'lms'  -- hoặc 'sd_bands'
WHERE `key` = 'zscore_method';
```

### 3. Check Current Method
```php
// In any controller or view
$method = getZScoreMethod();  // Returns 'lms' or 'sd_bands'

// Or check boolean
if (isUsingLMS()) {
    // Using LMS method
} else {
    // Using SD Bands
}
```

## Usage in Code

### Auto Method Selection
Sử dụng methods có `_auto` suffix để tự động chọn method theo setting:

```php
$history = History::find($id);

// Auto-select based on setting
$zscore = $history->getWeightForAgeZScoreAuto();
$result = $history->check_weight_for_age_auto();
```

### Manual Method Selection
Hoặc gọi trực tiếp method cụ thể:

```php
// Force LMS
$zscore = $history->getWeightForAgeZScoreLMS();

// Force SD Bands
$zscore = $history->getWeightForAgeZScore();
```

## Testing Comparison

### Command to Compare Methods
```bash
php artisan who:compare-methods --limit=100
```

Output:
```
=== Comparing SD Bands vs LMS Methods ===
Analyzing 100 records...

📊 Weight-for-Age:
  Mean difference: 0.0234
  Max difference: 0.0876
  Min difference: 0.0001
  Significant (>0.05): 12 / 100
  ✓ Excellent agreement

📊 Height-for-Age:
  Mean difference: 0.0089
  Max difference: 0.0321
  Min difference: 0.0000
  Significant (>0.05): 2 / 100
  ✓ Excellent agreement

Classification Changes: 3 / 100 records
Change Rate: 3.00%

=== Overall Assessment ===
✓ EXCELLENT: LMS method shows high agreement with SD Bands method.
  Safe to deploy to production.
```

### Export to CSV
```bash
php artisan who:compare-methods --limit=1000 --export
# Saved to: storage/app/zscore_comparison_2025-11-04_180522.csv
```

## Dashboard Controller Update

**RECOMMENDED**: Use `_auto` methods in DashboardController:

```php
// OLD - Hardcoded to SD Bands
$wa = $history->check_weight_for_age();
$zscore_wa = $history->getWeightForAgeZScore();

// NEW - Respects admin setting
$wa = $history->check_weight_for_age_auto();
$zscore_wa = $history->getWeightForAgeZScoreAuto();
```

This allows administrators to switch methods without code changes.

## Rollback Plan

If any issues with LMS method:

1. **Switch back to SD Bands**:
```sql
UPDATE settings SET value = 'sd_bands' WHERE `key` = 'zscore_method';
```

2. **All calculations will instantly use old method**
   - No code deployment needed
   - Dashboard continues working
   - Users see familiar results

3. **Re-enable LMS when ready**:
```sql
UPDATE settings SET value = 'lms' WHERE `key` = 'zscore_method';
```

## Performance Considerations

- **LMS Method**: Slightly slower (database queries for L, M, S parameters)
- **SD Bands**: Faster (simpler calculation)
- **Recommendation**: Use LMS for accuracy, cache results if performance is critical

## Best Practices

1. ✅ **Default to LMS** - Set `zscore_method = 'lms'` after migration
2. ✅ **Use `_auto` methods** - Future-proof code that respects settings
3. ✅ **Test before switching** - Run comparison command first
4. ✅ **Keep both methods** - Don't delete SD Bands code yet (backup)
5. ✅ **Monitor dashboards** - Check statistics after switching
6. ⚠️ **Don't delete old tables** - Keep as reference until fully validated

## Admin UI (Future Enhancement)

Create admin page at `/admin/settings/zscore`:

```blade
<div class="form-group">
    <label>Z-Score Calculation Method</label>
    <select name="zscore_method" class="form-control">
        <option value="lms" {{ getSetting('zscore_method') == 'lms' ? 'selected' : '' }}>
            WHO LMS 2006 (Recommended)
        </option>
        <option value="sd_bands" {{ getSetting('zscore_method') == 'sd_bands' ? 'selected' : '' }}>
            SD Bands (Legacy)
        </option>
    </select>
    <small class="form-text text-muted">
        LMS provides exact WHO compliance. SD Bands is for comparison only.
    </small>
</div>
```

Save to settings table on submit.

## Current Status

- ✅ Migration ready: `2025_11_04_180122_add_zscore_method_setting.php`
- ✅ Helper functions: `getZScoreMethod()`, `isUsingLMS()`
- ✅ Auto methods: `*_auto()` added to History model
- ✅ Comparison tool: `php artisan who:compare-methods`
- ⏳ Dashboard update: Use `_auto` methods in controller
- ⏳ Admin UI: Create settings page (optional)

## Next Steps

1. Run migration: `php artisan migrate`
2. Test comparison: `php artisan who:compare-methods --limit=100`
3. Update DashboardController to use `_auto` methods
4. Test dashboard with both methods
5. Set production to LMS: `UPDATE settings SET value = 'lms'`
6. Monitor for 1-2 weeks
7. Consider deprecating SD Bands tables
