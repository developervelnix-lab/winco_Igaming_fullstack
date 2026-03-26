const fs = require('fs');
const path = require('path');

const targetDirs = [
  path.join(__dirname, 'src/components/sidebar-components'),
  path.join(__dirname, 'src/components/navbar')
];

function walkDir(dir, callback) {
  if (!fs.existsSync(dir)) return;
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
  });
}

function processFile(filePath) {
  if (!filePath.endsWith('.jsx')) return;
  let content = fs.readFileSync(filePath, 'utf8');
  let originalContent = content;

  // Replace #111111 and #000000 in styles
  content = content.replace(/backgroundColor:\s*["']#111111["']/g, 'backgroundColor: COLORS.bg2');
  content = content.replace(/backgroundColor:\s*["']#000000["']/g, 'backgroundColor: COLORS.bg3');
  
  // Replace text colors that accompany those backgrounds
  content = content.replace(/color:\s*["']#FFFFFF["']/g, 'color: COLORS.text');
  content = content.replace(/color:\s*["']#E6A000["']/g, 'color: COLORS.brand');

  // Replace bg-black class with bg-gray-100 dark:bg-black but ONLY if it's the exact class "bg-black" alone (not bg-black/10)
  // We can use regex with word boundaries but we need to ensure it's not followed by a slash.
  content = content.replace(/\bbg-black(?!\/)/g, 'bg-gray-100 dark:bg-black');

  // Verify that COLORS is imported/used if we injected COLORS
  if (content !== originalContent && content.includes('COLORS.') && !content.includes('useColors')) {
    // If it doesn't have useColors, this is a risk. But we ensured previously that they do, or we will log it.
    console.log(`WARNING: ${filePath} might be missing useColors()`);
  }

  if (content !== originalContent) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
  }
}

targetDirs.forEach(dir => walkDir(dir, processFile));
