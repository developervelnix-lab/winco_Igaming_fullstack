const fs = require('fs');
const path = require('path');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
  });
}

function processFile(filePath) {
  if (!filePath.endsWith('.jsx')) return;
  const content = fs.readFileSync(filePath, 'utf8');
  if (!content.includes('COLORS.')) return;

  const lines = content.split('\n');
  let openBraces = 0;
  let inComponent = false;
  let hasUseColors = false;
  let hasCOLORSUsage = false;
  let componentName = '';

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];

    // Detect function / component boundary
    if ((line.includes('const ') && line.includes(' = (') && line.includes('=> {')) || 
        (line.startsWith('function ') && line.includes('('))) {
      inComponent = true;
      openBraces = 0;
      hasUseColors = false;
      hasCOLORSUsage = false;
      componentName = line.split('(')[0].replace('const ', '').replace('export ', '').replace('function ', '').replace(' = ', '').trim();
    }

    if (inComponent) {
      if (line.includes('{')) openBraces++;
      if (line.includes('}')) openBraces--;
      
      if (line.includes('useColors()')) hasUseColors = true;
      if (line.includes('COLORS.')) hasCOLORSUsage = true;

      // Close component
      if (openBraces === 0) {
        if (hasCOLORSUsage && !hasUseColors) {
            console.log(`Missing useColors in ${componentName} at ${filePath}`);
        }
        inComponent = false;
        hasUseColors = false;
        hasCOLORSUsage = false;
      }
    }
  }
}

walkDir(path.join(__dirname, 'src/components'), processFile);
