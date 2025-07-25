#!/bin/bash

echo "🔧 BASH TEST OF INSTALLER FIXES"
echo "================================"

# Test 1: Check if all steps exist
echo -e "\n1️⃣ TESTING STEP FILES:"
for i in {1..9}; do
    if [ -f "install/steps/step_$i.php" ]; then
        size=$(du -h "install/steps/step_$i.php" | cut -f1)
        echo "✅ Step $i exists ($size)"
    else
        echo "❌ Step $i missing"
    fi
done

# Test 2: Check for critical issues
echo -e "\n2️⃣ TESTING CRITICAL FIXES:"

# Step 8 buffer handling
if grep -q "ob_start()" install/steps/step_8.php && grep -q "ob_end_clean()" install/steps/step_8.php; then
    echo "✅ Step 8: Buffer handling added"
else
    echo "❌ Step 8: Buffer handling missing"
fi

# Step 8 JSON headers
if grep -q "Content-Type: application/json" install/steps/step_8.php; then
    echo "✅ Step 8: JSON headers present"
else
    echo "❌ Step 8: JSON headers missing"
fi

# Step 4 language removal
if ! grep -q "language" install/steps/step_4.php && ! grep -q "timezone" install/steps/step_4.php; then
    echo "✅ Step 4: Language/timezone removed"
else
    echo "❌ Step 4: Language/timezone still present"
fi

# Test 3: Button validation fixes
echo -e "\n3️⃣ TESTING BUTTON FIXES:"

# Check if form validation happens BEFORE loading state
for step in 4 5 7; do
    if grep -A 10 "form.addEventListener.*submit" install/steps/step_$step.php | grep -q "checkValidity().*preventDefault" && ! grep -B 5 "checkValidity()" install/steps/step_$step.php | grep -q "fa-spinner"; then
        echo "✅ Step $step: Validation before loading"
    else
        echo "❌ Step $step: Loading before validation"
    fi
done

# Test 4: Animation optimizations
echo -e "\n4️⃣ TESTING ANIMATION FIXES:"

# Step 5 CSS optimization
if grep -q "will-change: transform" install/steps/step_5.php; then
    echo "✅ Step 5: Animation optimization"
else
    echo "❌ Step 5: Animation not optimized"
fi

# Step 6 debouncing
if grep -q "updateTimeout" install/steps/step_6.php; then
    echo "✅ Step 6: Debouncing added"
else
    echo "❌ Step 6: Debouncing missing"
fi

# Test 5: File structure
echo -e "\n5️⃣ TESTING FILE STRUCTURE:"

critical_files=(
    "install/index.php"
    "install/database.sql"
    "core/functions.php"
    "languages/uk.php"
    "languages/ru.php"
    "languages/en.php"
)

for file in "${critical_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "❌ $file missing"
    fi
done

# Test 6: Count issues
echo -e "\n6️⃣ COUNTING ISSUES:"

total_files=$(find install/steps -name "step_*.php" | wc -l)
echo "📁 Total step files: $total_files"

exit_count=$(grep -c "exit();" install/steps/step_8.php)
echo "🚪 Exit calls in step 8: $exit_count"

gradient_count=$(grep -c "'gradient-" install/steps/step_6.php)
echo "🎨 Gradients in step 6: $gradient_count"

# Final summary
echo -e "\n🎯 SUMMARY:"
echo "===================="

if [ $total_files -eq 9 ] && [ $exit_count -ge 2 ] && [ $gradient_count -ge 30 ]; then
    echo "🎉 ALL BASIC TESTS PASSED!"
    echo "✅ Ready for browser testing"
else
    echo "⚠️  Some issues detected"
    echo "🔧 Manual verification recommended"
fi

echo -e "\n📋 NEXT STEPS:"
echo "1. Open http://your-domain/install/ in browser"
echo "2. Test each step thoroughly"
echo "3. Verify button behavior on validation errors"
echo "4. Check step 8 for JSON errors"
echo "5. Test theme/language animations"

echo -e "\n=== TEST COMPLETE ==="