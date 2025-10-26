/**
 * Fix BMI calculation for all age groups (0-5, 5-19, 19+)
 * Original code only calculated BMI for category > 1 (excluding 0-5 age group)
 * WHO standards require BMI-for-age calculation for ALL children 0-19 years
 * 
 * Created: 27/10/2025
 */

$(document).ready(function() {
    // Remove the category > 1 condition - calculate BMI for ALL age groups
    
    // Event: When height is entered, calculate BMI if weight exists
    $("#length-user-profile").keyup(function() {
        if ($("#weight-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
    
    // Event: When weight is entered, calculate BMI if height exists
    $("#weight-user-profile").keyup(function() {
        if ($("#length-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
    
    // Also trigger calculation on change event (for autofill, paste, etc.)
    $("#length-user-profile, #weight-user-profile").change(function() {
        if ($("#weight-user-profile").val().length > 0 && $("#length-user-profile").val().length > 0) {
            $("#bmi-user-profile").val(
                bmiCalculate(
                    $("#weight-user-profile").val(),
                    $("#length-user-profile").val()
                )
            );
        }
    });
});

/**
 * Calculate BMI (Body Mass Index)
 * Formula: BMI = weight (kg) / [height (m)]²
 * 
 * @param {number} $weight - Weight in kilograms
 * @param {number} $length - Height in centimeters
 * @returns {number} BMI value rounded to 1 decimal place
 */
function bmiCalculate($weight, $length) {
    // Convert height from cm to m
    var heightInMeters = $length / 100;
    
    // Calculate BMI = kg / m²
    var value = $weight / (heightInMeters * heightInMeters);
    
    // Round to 1 decimal place
    return Math.floor(value * 10) / 10;
}
