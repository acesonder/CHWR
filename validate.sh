#!/bin/bash

# CHWR Application Validation Script
# This script validates the installation and configuration

echo "======================================"
echo "CHWR Application Validation"
echo "======================================"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Track errors
ERRORS=0

# Check PHP
echo "1. Checking PHP installation..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    echo -e "${GREEN}✓${NC} PHP is installed: $PHP_VERSION"
else
    echo -e "${RED}✗${NC} PHP is not installed"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# Check PHP version
echo "2. Checking PHP version..."
PHP_VERSION_NUM=$(php -r 'echo PHP_VERSION;' 2>/dev/null)
if [ ! -z "$PHP_VERSION_NUM" ]; then
    MAJOR_VERSION=$(echo $PHP_VERSION_NUM | cut -d. -f1)
    MINOR_VERSION=$(echo $PHP_VERSION_NUM | cut -d. -f2)
    if [ "$MAJOR_VERSION" -gt 7 ] || ([ "$MAJOR_VERSION" -eq 7 ] && [ "$MINOR_VERSION" -ge 4 ]); then
        echo -e "${GREEN}✓${NC} PHP version is compatible: $PHP_VERSION_NUM (requires 7.4+)"
    else
        echo -e "${YELLOW}⚠${NC} PHP version may be too old: $PHP_VERSION_NUM (requires 7.4+)"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo -e "${RED}✗${NC} Could not determine PHP version"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# Check required PHP extensions
echo "3. Checking PHP extensions..."
REQUIRED_EXTENSIONS=("mysqli" "session" "json")
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo -e "${GREEN}✓${NC} Extension '$ext' is installed"
    else
        echo -e "${RED}✗${NC} Extension '$ext' is missing"
        ERRORS=$((ERRORS + 1))
    fi
done
echo ""

# Check file structure
echo "4. Checking file structure..."
REQUIRED_FILES=(
    "index.php"
    "login.php"
    "logout.php"
    "dashboard.php"
    "users.php"
    "audit-log.php"
    "config/database.php"
    "includes/auth.php"
    "includes/header.php"
    "includes/footer.php"
    "api/users.php"
    "database/schema.sql"
    "database/setup.php"
    "public/css/styles.css"
    "public/js/main.js"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} File exists: $file"
    else
        echo -e "${RED}✗${NC} File missing: $file"
        ERRORS=$((ERRORS + 1))
    fi
done
echo ""

# Check file permissions
echo "5. Checking file permissions..."
if [ -r "config/database.php" ]; then
    echo -e "${GREEN}✓${NC} config/database.php is readable"
else
    echo -e "${RED}✗${NC} config/database.php is not readable"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# Validate PHP syntax
echo "6. Validating PHP syntax..."
SYNTAX_ERRORS=0
for file in $(find . -name "*.php" -not -path "./.git/*"); do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo -e "${RED}✗${NC} Syntax error in: $file"
        SYNTAX_ERRORS=$((SYNTAX_ERRORS + 1))
    fi
done

if [ $SYNTAX_ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓${NC} All PHP files have valid syntax"
else
    echo -e "${RED}✗${NC} Found $SYNTAX_ERRORS PHP files with syntax errors"
    ERRORS=$((ERRORS + SYNTAX_ERRORS))
fi
echo ""

# Check MySQL connection
echo "7. Checking database configuration..."
if [ -f "config/database.php" ]; then
    echo -e "${GREEN}✓${NC} Database configuration file exists"
    echo -e "${YELLOW}⚠${NC} Note: Actual database connection cannot be tested without MySQL running"
else
    echo -e "${RED}✗${NC} Database configuration file missing"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# Check documentation
echo "8. Checking documentation..."
DOC_FILES=("README.md" "INSTALLATION.md" "FEATURES.md" "UI-GUIDE.md")
for doc in "${DOC_FILES[@]}"; do
    if [ -f "$doc" ]; then
        echo -e "${GREEN}✓${NC} Documentation exists: $doc"
    else
        echo -e "${YELLOW}⚠${NC} Documentation missing: $doc"
    fi
done
echo ""

# Summary
echo "======================================"
echo "Validation Summary"
echo "======================================"

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Configure your database credentials in config/database.php"
    echo "2. Run: php database/setup.php"
    echo "3. Set up your web server to point to this directory"
    echo "4. Access the application at http://localhost/"
    echo "5. Login with: admin / admin123"
else
    echo -e "${RED}✗ Found $ERRORS error(s)${NC}"
    echo ""
    echo "Please fix the errors above before proceeding."
fi
echo ""

exit $ERRORS
