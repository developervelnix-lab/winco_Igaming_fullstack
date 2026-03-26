const fs = require('fs');
const path = require('path');

const authDir = path.join(__dirname, 'src/components/auth');

function processFile(filePath) {
  if (!filePath.endsWith('.jsx')) return;
  let content = fs.readFileSync(filePath, 'utf8');
  let originalContent = content;

  // We want to replace exactly:
  // style={{ fontFamily: FONTS.ui, color: 'white', backgroundColor: 'rgba(255, 255, 255, 0.05)' }}
  // with
  // style={{ fontFamily: FONTS.ui }}
  // OR with paddingLeft:
  // style={{ fontFamily: FONTS.ui, color: 'white', backgroundColor: 'rgba(255, 255, 255, 0.05)', paddingLeft: '70px' }}
  // with
  // style={{ fontFamily: FONTS.ui, paddingLeft: '70px' }}

  content = content.replace(/style=\{\{\s*fontFamily:\s*FONTS\.ui,\s*color:\s*'white',\s*backgroundColor:\s*'rgba\(255,\s*255,\s*255,\s*0\.05\)'\s*\}\}/g, 
                            "style={{ fontFamily: FONTS.ui }}");

  content = content.replace(/style=\{\{\s*fontFamily:\s*FONTS\.ui,\s*color:\s*'white',\s*backgroundColor:\s*'rgba\(255,\s*255,\s*255,\s*0\.05\)',\s*paddingLeft:\s*'70px'\s*\}\}/g, 
                            "style={{ fontFamily: FONTS.ui, paddingLeft: '70px' }}");

  if (content !== originalContent) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Updated: ${filePath}`);
  }
}

fs.readdirSync(authDir).forEach(f => {
  processFile(path.join(authDir, f));
});
