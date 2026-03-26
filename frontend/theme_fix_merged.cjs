/**
 * Fix merged import lines where the script joined
 * import statement with next code on same line.
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
            if (file !== 'node_modules') {
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
let fixedFiles = 0;

files.forEach(filePath => {
    let content = fs.readFileSync(filePath, 'utf8');
    const original = content;

    // Fix pattern: import ... from '...theme';const/function/export
    // Split merged import+code lines
    content = content.replace(
        /from\s+['"]([^'"]+)['"];(const |function |export |import |class )/g,
        "from '$1';\n$2"
    );

    // Fix pattern: import ... from '...useColors';const/function/export
    content = content.replace(
        /from\s+['"]([^'"]+)['"];(const |function |export |import |class )/g,
        "from '$1';\n$2"
    );

    // Now ensure every component that uses COLORS has const COLORS = useColors()
    // Check if file has useColors import but no const COLORS = useColors() call
    if (content.includes("import { useColors }") && !content.includes("const COLORS = useColors()")) {
        // Find component function declarations and add useColors() call
        const lines = content.split('\n');
        const newLines = [];
        
        for (let i = 0; i < lines.length; i++) {
            newLines.push(lines[i]);
            const trimmed = lines[i].trim();
            
            // Check if this line starts a component function
            if (
                (trimmed.match(/^(export\s+)?(default\s+)?function\s+\w+/) ||
                 trimmed.match(/^(export\s+)?(const|let|var)\s+\w+\s*=\s*\(/) ||
                 trimmed.match(/^const\s+\w+\s*=\s*\(\{/)) &&
                trimmed.includes('{')
            ) {
                // Get indentation of the next non-empty line
                let indent = '  ';
                for (let j = i + 1; j < Math.min(i + 5, lines.length); j++) {
                    if (lines[j].trim().length > 0) {
                        const match = lines[j].match(/^(\s*)/);
                        if (match) indent = match[1];
                        break;
                    }
                }
                newLines.push(`${indent}const COLORS = useColors();`);
            }
        }
        
        content = newLines.join('\n');
    }

    if (content !== original) {
        fs.writeFileSync(filePath, content, 'utf8');
        fixedFiles++;
        console.log(`Fixed: ${path.relative(__dirname, filePath)}`);
    }
});

console.log(`\nTotal: Fixed ${fixedFiles} files.`);
