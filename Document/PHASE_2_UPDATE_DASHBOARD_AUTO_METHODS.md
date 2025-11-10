# Phase 2: Update Dashboard to Use Auto-Switching Methods

**Mục tiêu:** Cập nhật DashboardController để sử dụng `*_auto()` methods, cho phép dashboard tự động chuyển đổi giữa LMS và SD Bands theo setting `zscore_method`.

---

## 📋 Overview

### Current Status (After Cleanup)
- ✅ Tables 9a và 10a đã được loại bỏ (1,008 lines removed)
- ✅ Views và Controller đã clean, không còn comparison tables
- ⏳ Dashboard vẫn sử dụng hardcoded old methods (`getWeightForAgeZScore()`, etc.)
- ⏳ Chưa tích hợp Auto-Switching System

### Goal
Update tất cả method calls trong DashboardController để:
1. Sử dụng `*_auto()` versions thay vì old hardcoded methods
2. Dashboard tự động respect setting `zscore_method`
3. Khi user chọn LMS → tất cả tính toán dùng LMS
4. Khi user chọn SD Bands → tất cả tính toán dùng SD Bands (default)

---

## 🔄 Methods to Replace

### 1. Z-Score Getter Methods

#### Weight-for-Age Z-Score
```php
// ❌ OLD (Hardcoded SD Bands)
$waZscore = $child->getWeightForAgeZScore();

// ✅ NEW (Auto-switching)
$waZscore = $child->getWeightForAgeZScoreAuto();
```

#### Height-for-Age Z-Score
```php
// ❌ OLD (Hardcoded SD Bands)
$haZscore = $child->getHeightForAgeZScore();

// ✅ NEW (Auto-switching)
$haZscore = $child->getHeightForAgeZScoreAuto();
```

#### Weight-for-Height Z-Score
```php
// ❌ OLD (Hardcoded SD Bands)
$whZscore = $child->getWeightForHeightZScore();

// ✅ NEW (Auto-switching)
$whZscore = $child->getWeightForHeightZScoreAuto();
```

#### BMI-for-Age Z-Score
```php
// ❌ OLD (Hardcoded SD Bands)
$bmiZscore = $child->getBMIForAgeZScore();

// ✅ NEW (Auto-switching)
$bmiZscore = $child->getBMIForAgeZScoreAuto();
```

---

### 2. Check Methods (Classification)

#### Weight-for-Age Classification
```php
// ❌ OLD (Hardcoded SD Bands)
$weightForAge = $record->check_weight_for_age();
$result = $record->check_weight_for_age()['result'];

// ✅ NEW (Auto-switching)
$weightForAge = $record->check_weight_for_age_auto();
$result = $record->check_weight_for_age_auto()['result'];
```

#### Height-for-Age Classification
```php
// ❌ OLD (Hardcoded SD Bands)
$heightForAge = $record->check_height_for_age();
$result = $record->check_height_for_age()['result'];

// ✅ NEW (Auto-switching)
$heightForAge = $record->check_height_for_age_auto();
$result = $record->check_height_for_age_auto()['result'];
```

#### Weight-for-Height Classification
```php
// ❌ OLD (Hardcoded SD Bands)
$weightForHeight = $record->check_weight_for_height();
$result = $record->check_weight_for_height()['result'];

// ✅ NEW (Auto-switching)
$weightForHeight = $record->check_weight_for_height_auto();
$result = $record->check_weight_for_height_auto()['result'];
```

---

## 📍 Locations to Update

### Identified from Previous Grep Search

**File:** `app/Http/Controllers/Admin/DashboardController.php`

**Estimated locations (~20-30 calls):**

1. **Line ~226:** `check_weight_for_age()` and `check_height_for_age()`
2. **Line ~1000-1002:** Z-score getter methods
3. **Lines ~1217, 1276, 1354, 1355:** More Z-score calls
4. **Lines ~1446-1448:** Z-score getter methods
5. **Lines ~1538, 1597:** Z-score calls
6. **Lines ~1675-1676, 1767:** More Z-score methods

### Helper Methods to Update

Cần update các helper methods sau trong DashboardController:

1. **getWeightForAgeStatistics($records)**
2. **getHeightForAgeStatistics($records)**
3. **getWeightForHeightStatistics($records)**
4. **getMeanZScoreStatistics($records)**
5. **getWHOCombinedMalnutritionStats($records)**
6. **getWHOMaleMalnutritionStats($records)**
7. **getWHOFemaleMalnutritionStats($records)**
8. **getWHOCombinedByEthnicStats($records)**
9. **getNutritionStatsUnder24Months($records)** ← Bảng 9
10. **getNutritionStatsUnder60Months($records)** ← Bảng 10

**Tất cả helper methods này đều call Z-score methods và cần update.**

---

## 🔍 Search Strategy

### Step 1: Identify All Old Method Calls

```bash
# Search for old Z-score getter methods
grep -n "getWeightForAgeZScore()" DashboardController.php
grep -n "getHeightForAgeZScore()" DashboardController.php  
grep -n "getWeightForHeightZScore()" DashboardController.php
grep -n "getBMIForAgeZScore()" DashboardController.php

# Search for old check methods
grep -n "check_weight_for_age()" DashboardController.php
grep -n "check_height_for_age()" DashboardController.php
grep -n "check_weight_for_height()" DashboardController.php
```

### Step 2: Replace Systematically

**Strategy:** Replace by helper method, one at a time
1. Read the complete helper method
2. Identify all old method calls within it
3. Replace with auto versions
4. Verify syntax
5. Move to next helper method

**Order of replacement (suggested):**
1. getWeightForAgeStatistics()
2. getHeightForAgeStatistics()
3. getWeightForHeightStatistics()
4. getMeanZScoreStatistics()
5. getWHOCombinedMalnutritionStats()
6. getWHOMaleMalnutritionStats()
7. getWHOFemaleMalnutritionStats()
8. getWHOCombinedByEthnicStats()
9. getNutritionStatsUnder24Months()
10. getNutritionStatsUnder60Months()

---

## ⚠️ Important Considerations

### 1. Backward Compatibility
- **No migration needed:** Old records don't need re-calculation
- **Auto methods handle both:** LMS and SD Bands work on existing data
- **Setting determines behavior:** Change setting → all calculations change

### 2. Cell Details Functionality
- Ensure `cell-details` AJAX endpoints also use auto methods
- Check `getCellDetails()` method if exists
- Update any JSON responses that include Z-scores

### 3. Export Functionality
- CSV exports should use auto methods
- Excel exports should use auto methods
- Verify export headers still correct

### 4. Performance
- Auto methods add minimal overhead (~1 DB read for setting)
- Setting is cached by `getSetting()` helper
- No performance degradation expected

---

## 🧪 Testing Checklist

### Before Starting
- [ ] Backup current DashboardController.php
- [ ] Note current dashboard load time
- [ ] Export sample data with old methods for comparison

### During Implementation
- [ ] Replace methods in one helper at a time
- [ ] Check PHP syntax after each change
- [ ] Test affected table in browser

### After Completion
- [ ] **Test with SD Bands (default):**
  - [ ] All 10 tables calculate correctly
  - [ ] Cell-details popups work
  - [ ] CSV export works
  - [ ] Compare with old export data (should be identical)

- [ ] **Switch to LMS:**
  - [ ] Change setting to `zscore_method = 'lms'`
  - [ ] Refresh dashboard
  - [ ] Verify numbers changed (LMS gives different Z-scores)
  - [ ] Check tables 1-10 all show LMS calculations
  - [ ] Cell-details use LMS
  - [ ] CSV export uses LMS

- [ ] **Switch back to SD Bands:**
  - [ ] Change setting to `zscore_method = 'sd_bands'`
  - [ ] Refresh dashboard
  - [ ] Verify numbers match original SD Bands export

### Data Validation
- [ ] Run `php artisan who:compare-methods` command
- [ ] Review differences report
- [ ] Spot-check 10-20 random records manually
- [ ] Verify means and percentages make sense

### Performance Testing
- [ ] Measure dashboard load time with auto methods
- [ ] Check database query count
- [ ] Monitor memory usage
- [ ] Test with large datasets (1000+ records)

---

## 📊 Expected Changes After Update

### What Will Change
✅ **Z-score calculations:** Will use method specified in `zscore_method` setting
✅ **Classification results:** May differ between LMS and SD Bands for boundary cases
✅ **Statistics:** Percentages and counts will reflect chosen method
✅ **Cell-details:** Will show correct Z-scores for chosen method

### What Will NOT Change
✅ **UI/UX:** Dashboard layout remains the same
✅ **Filtering:** Date range, location, ethnic filters unchanged
✅ **Tables:** Still 10 tables (Tables 1-10, no 9a/10a)
✅ **Export:** CSV format unchanged, just data values differ

---

## 🚀 Implementation Steps

### Step 1: Grep Search for All Old Calls
```bash
# Get line numbers and counts
grep -n "getWeightForAgeZScore()" DashboardController.php | wc -l
grep -n "getHeightForAgeZScore()" DashboardController.php | wc -l
grep -n "getWeightForHeightZScore()" DashboardController.php | wc -l
grep -n "getBMIForAgeZScore()" DashboardController.php | wc -l
grep -n "check_weight_for_age()" DashboardController.php | wc -l
grep -n "check_height_for_age()" DashboardController.php | wc -l
grep -n "check_weight_for_height()" DashboardController.php | wc -l
```

### Step 2: Update getWeightForAgeStatistics()
1. Read method content
2. Find all `getWeightForAgeZScore()` calls
3. Replace with `getWeightForAgeZScoreAuto()`
4. Check syntax
5. Test Table 1 in browser

### Step 3: Update getHeightForAgeStatistics()
1. Read method content
2. Find all `getHeightForAgeZScore()` calls
3. Replace with `getHeightForAgeZScoreAuto()`
4. Check syntax
5. Test Table 2 in browser

### Step 4: Update getWeightForHeightStatistics()
1. Read method content
2. Find all `getWeightForHeightZScore()` calls
3. Replace with `getWeightForHeightZScoreAuto()`
4. Check syntax
5. Test Table 3 in browser

### Step 5: Update getMeanZScoreStatistics()
1. Read method content
2. Replace all Z-score getters with auto versions
3. Check syntax
4. Test Table 4 in browser

### Step 6: Update WHO Methods (Tables 5-8)
1. Update getWHOCombinedMalnutritionStats()
2. Update getWHOMaleMalnutritionStats()
3. Update getWHOFemaleMalnutritionStats()
4. Update getWHOCombinedByEthnicStats()
5. Test Tables 5-8 in browser

### Step 7: Update Nutrition Stats Methods (Tables 9-10)
1. Update getNutritionStatsUnder24Months() → Bảng 9
2. Update getNutritionStatsUnder60Months() → Bảng 10
3. Test Tables 9-10 in browser

### Step 8: Final Verification
1. Check all 10 tables with SD Bands
2. Switch to LMS and verify changes
3. Run comparison command
4. Export CSV and compare data

---

## 📝 Documentation Updates Needed

After completing Phase 2:

1. **Update HUONG_DAN_UPDATE_DASHBOARD_LMS.md**
   - Add actual implementation details
   - Document any issues encountered
   - List exact line numbers changed

2. **Update LMS_IMPLEMENTATION_SUMMARY.md**
   - Mark Phase 2 as complete
   - Add testing results
   - Document performance metrics

3. **Create CHANGELOG.md entry**
   - Version bump (e.g., v2.1.0)
   - List breaking changes (if any)
   - Document new features

4. **Update README.md (if needed)**
   - Add note about Z-Score method switching
   - Link to UI_GUIDE_ZSCORE_SWITCHING.md

---

## 🎯 Success Criteria

### Must Have
- [ ] All 10 tables use auto methods
- [ ] Dashboard respects `zscore_method` setting
- [ ] No PHP errors or warnings
- [ ] Cell-details functionality works
- [ ] CSV export uses auto methods

### Should Have
- [ ] Performance equal or better than before
- [ ] Documentation updated
- [ ] Testing completed for both methods
- [ ] Comparison report generated

### Nice to Have
- [ ] Visual indicator showing current method in dashboard
- [ ] "Switch method and refresh" button in dashboard
- [ ] Method comparison view within dashboard

---

## 🔧 Rollback Plan

If issues occur:

1. **Immediate rollback:**
   ```bash
   git checkout HEAD -- app/Http/Controllers/Admin/DashboardController.php
   ```

2. **Partial rollback:**
   - Keep working helper methods
   - Revert problematic ones
   - Fix issues then re-apply

3. **Database rollback:**
   - No database changes in Phase 2
   - Just code changes
   - Safe to rollback anytime

---

## ✅ Ready to Start

**Prerequisites met:**
- ✅ Tables 9a and 10a removed
- ✅ Auto-Switching System implemented in History model
- ✅ Helper functions available in common.php
- ✅ Admin UI for switching methods complete

**Files to modify:**
1. `app/Http/Controllers/Admin/DashboardController.php` (main work)
2. Documentation files (after completion)

**Estimated time:** 2-3 hours
**Risk level:** Low (easy rollback, no DB changes)
**Impact:** High (dashboard becomes dynamic with auto-switching)

**Next command to run:**
```bash
# Start with grep searches to identify all locations
grep -n "getWeightForAgeZScore()" app/Http/Controllers/Admin/DashboardController.php
```
