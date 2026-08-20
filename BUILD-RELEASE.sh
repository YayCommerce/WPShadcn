#!/bin/bash
# Build a production-ready release of the Shadcn WordPress Theme
# This script creates a ZIP file excluding all development tools

set -e

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  Shadcn WordPress Theme - Release Builder${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

echo "Generating build directory..."
rm -rf "$(pwd)/release"
mkdir -p "$(pwd)/release"

# Get theme directory
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_NAME="shadcn"
BUILD_DIR="$THEME_DIR/release"

# Extract version from style.css
if [ -f "$THEME_DIR/style.css" ]; then
    VERSION=$(grep -i "^Version:" "$THEME_DIR/style.css" | head -1 | awk '{print $2}' | tr -d '\r')
    if [ -z "$VERSION" ]; then
        VERSION="1.0.0"
        echo -e "${YELLOW}⚠️  Warning: Could not find version in style.css, using default: $VERSION${NC}"
    fi
else
    VERSION="1.0.0"
    echo -e "${RED}❌ Error: style.css not found, using default version: $VERSION${NC}"
fi

ZIP_NAME="${THEME_NAME}-${VERSION}.zip"

echo -e "${YELLOW}📦 Theme Directory:${NC} $THEME_DIR"
echo -e "${YELLOW}🏷️  Theme Version:${NC} $VERSION"
echo -e "${YELLOW}📁 Build Directory:${NC} $BUILD_DIR"
echo -e "${YELLOW}📝 Output File:${NC} $ZIP_NAME"
echo ""

# Create build directory
echo -e "${BLUE}Creating build directory...${NC}"
mkdir -p "$BUILD_DIR"

# Check if .distignore exists
if [ ! -f "$THEME_DIR/.distignore" ]; then
    echo -e "${RED}❌ Error: .distignore file not found${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Found .distignore${NC}"

# Count files to be excluded
EXCLUDE_COUNT=$(grep -v '^#' "$THEME_DIR/.distignore" | grep -v '^$' | wc -l | tr -d ' ')
echo -e "${YELLOW}📋 Excluding $EXCLUDE_COUNT patterns from build${NC}"
echo ""

# Build the ZIP file
echo -e "${BLUE}Building release ZIP...${NC}"

cd "$THEME_DIR"

# Create temporary directory for clean build
TEMP_DIR=$(mktemp -d)
TEMP_THEME_DIR="$TEMP_DIR/$THEME_NAME"

echo -e "${YELLOW}📂 Creating temporary build in:${NC} $TEMP_DIR"

# Copy all files
cp -R "$THEME_DIR" "$TEMP_THEME_DIR"

# Remove files listed in .distignore
echo -e "${BLUE}Removing development files...${NC}"

while IFS= read -r pattern; do
    # Skip comments and empty lines
    [[ "$pattern" =~ ^#.*$ ]] && continue
    [[ -z "$pattern" ]] && continue
    
    # Remove leading/trailing whitespace
    pattern=$(echo "$pattern" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')
    
    # Handle wildcards
    if [[ "$pattern" == *"*"* ]]; then
        find "$TEMP_THEME_DIR" -path "$TEMP_THEME_DIR/$pattern" -delete 2>/dev/null || true
    else
        # Remove trailing slash for consistency
        pattern="${pattern%/}"
        rm -rf "$TEMP_THEME_DIR/$pattern" 2>/dev/null || true
    fi
    
    echo -e "${GREEN}  ✓${NC} Removed: $pattern"
done < "$THEME_DIR/.distignore"

echo ""

# Count remaining files
FILE_COUNT=$(find "$TEMP_THEME_DIR" -type f | wc -l | tr -d ' ')
echo -e "${GREEN}✓ Production build contains $FILE_COUNT files${NC}"

# Calculate size
SIZE=$(du -sh "$TEMP_THEME_DIR" | cut -f1)
echo -e "${GREEN}✓ Build size: $SIZE${NC}"
echo ""

# Create ZIP
echo -e "${BLUE}Creating ZIP archive...${NC}"
cd "$TEMP_DIR"
zip -r "$BUILD_DIR/$ZIP_NAME" "$THEME_NAME" -q

# Cleanup
rm -rf "$TEMP_DIR"

# Verify ZIP
ZIP_SIZE=$(du -sh "$BUILD_DIR/$ZIP_NAME" | cut -f1)
echo -e "${GREEN}✓ Created: $BUILD_DIR/$ZIP_NAME ($ZIP_SIZE)${NC}"
echo ""

# Display what was excluded
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${BLUE}  Excluded from Release:${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${RED}  ✗ Development documentation${NC}"
echo -e "${RED}  ✗ Build tools and configs${NC}"
echo -e "${RED}  ✗ Test files${NC}"
echo ""

echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}  ✓ Release build complete!${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${YELLOW}📦 Release file:${NC} $BUILD_DIR/$ZIP_NAME"
echo -e "${YELLOW}🏷️  Version:${NC} $VERSION"
echo -e "${YELLOW}📊 File size:${NC} $ZIP_SIZE"
echo -e "${YELLOW}📁 Total files:${NC} $FILE_COUNT"
echo ""
echo -e "${GREEN}Ready to distribute! 🚀${NC}"
echo ""
