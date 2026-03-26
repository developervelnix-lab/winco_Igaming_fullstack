const fs = require('fs');
const path = require('path');

const authDir = path.join(__dirname, 'src/components/auth');

function processFile(filePath) {
  if (!filePath.endsWith('.jsx')) return;
  let content = fs.readFileSync(filePath, 'utf8');
  let originalContent = content;

  // Replace bg-gray-100 dark:bg-white/5 -> bg-black/5 dark:bg-white/5
  content = content.replace(/bg-gray-100 dark:bg-white\/5/g, 'bg-black/5 dark:bg-white/5');

  if (content !== originalContent) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
  }
}

fs.readdirSync(authDir).forEach(f => {
  processFile(path.join(authDir, f));
});
