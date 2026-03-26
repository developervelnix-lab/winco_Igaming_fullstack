/**
 * This script rewrites components that import COLORS from theme.js
 * to use the reactive useColors() hook instead.
 */
const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, 'src');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat && stat.isDirectory()) {
            if (file !== 'node_modules' && file !== 'context' && file !== 'hooks' && file !== 'constants') {
                results = results.concat(walk(filePath));
            }
        } else {
            if (filePath.endsWith('.jsx') || filePath.endsWith('.js')) {
                results.push(filePath);
            }
        }
    });
    return results;
}

const files = walk(srcDir);
let changedFiles = 0;

files.forEach(filePath => {
    let content = fs.readFileSync(filePath, 'utf8');
    const originalContent = content;

    // Skip files that don't import COLORS
    if (!content.includes('import') || !content.includes('COLORS')) return;
    // Skip ThemeSynchronizer - it has its own logic
    if (filePath.includes('ThemeSynchronizer')) return;
    // Skip files that already use useColors
    if (content.includes('useColors')) return;

    // Calculate relative path from file to hooks/useColors.js
    const fileDir = path.dirname(filePath);
    let relPath = path.relative(fileDir, path.join(srcDir, 'hooks', 'useColors.js')).replace(/\\/g, '/');
    if (!relPath.startsWith('.')) relPath = './' + relPath;
    // Remove .js extension for import
    relPath = relPath.replace(/\.js$/, '');

    // Pattern 1: import { COLORS, FONTS } from '...theme'
    const importBothRegex = /import\s*\{\s*COLORS\s*,\s*FONTS\s*\}\s*from\s*['"]([^'"]*theme)['"]\s*;?/;
    // Pattern 2: import { COLORS } from '...theme'
    const importColorsOnlyRegex = /import\s*\{\s*COLORS\s*\}\s*from\s*['"]([^'"]*theme)['"]\s*;?/;

    let hasColors = false;
    let hasFonts = false;

    if (importBothRegex.test(content)) {
        hasColors = true;
        hasFonts = true;
        const match = content.match(importBothRegex);
        const originalThemePath = match[1];
        content = content.replace(importBothRegex, 
            `import { useColors } from '${relPath}';\nimport { FONTS } from '${originalThemePath}';`
        );
    } else if (importColorsOnlyRegex.test(content)) {
        hasColors = true;
        content = content.replace(importColorsOnlyRegex, 
            `import { useColors } from '${relPath}';`
        );
    }

    if (!hasColors) return;

    // Now insert `const COLORS = useColors();` inside the component function body
    // We need to find the component function and add it after the opening
    
    // Try to find patterns like:
    // function ComponentName() {
    // const ComponentName = () => {
    // const ComponentName = function() {
    
    // Strategy: find the first line that has useState, useEffect, useNavigate, useLocation, 
    // or the first return statement, and insert before it
    
    const lines = content.split('\n');
    let insertIndex = -1;
    let indentation = '  ';
    
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].trim();
        // Look for the function/component opening
        if (line.match(/^(export\s+)?(default\s+)?function\s+\w+/) || 
            line.match(/^(export\s+)?(const|let|var)\s+\w+\s*=\s*(\(|function)/) ||
            line.match(/^const\s+\w+\s*=\s*\(\)\s*=>/)) {
            // Found component declaration, look for the opening brace
            for (let j = i; j < Math.min(i + 5, lines.length); j++) {
                if (lines[j].includes('{')) {
                    insertIndex = j + 1;
                    // Get indentation of next non-empty line
                    for (let k = j + 1; k < Math.min(j + 5, lines.length); k++) {
                        const nextLine = lines[k];
                        if (nextLine.trim().length > 0) {
                            const match = nextLine.match(/^(\s*)/);
                            if (match) indentation = match[1];
                            break;
                        }
                    }
                    break;
                }
            }
            if (insertIndex > -1) break;
        }
    }
    
    if (insertIndex > -1) {
        lines.splice(insertIndex, 0, `${indentation}const COLORS = useColors();`);
        content = lines.join('\n');
    }

    if (content !== originalContent) {
        fs.writeFileSync(filePath, content, 'utf8');
        changedFiles++;
        console.log(`Updated: ${path.relative(__dirname, filePath)}`);
    }
});

console.log(`\nTotal: Updated ${changedFiles} files to use useColors() hook.`);
