const fs = require('fs');
const path = require('path');

const srcDir = path.join(__dirname, 'src');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.jsx')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk(srcDir);
let changedFiles = 0;

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    const originalContent = content;

    // Backgrounds
    content = content.replace(/bg-\[#1A1A1A\]/g, 'bg-gray-100 dark:bg-[#1A1A1A]');
    content = content.replace(/!bg-\[#1A1A1A\]/g, '!bg-gray-100 dark:!bg-[#1A1A1A]');
    
    content = content.replace(/bg-\[#121212\]/g, 'bg-white dark:bg-[#121212]');
    content = content.replace(/!bg-\[#121212\]/g, '!bg-white dark:!bg-[#121212]');
    
    content = content.replace(/bg-\[#111111\]/g, 'bg-gray-50 dark:bg-[#111111]');
    
    // Borders
    content = content.replace(/border-white\/(\d+)/g, 'border-black/$1 dark:border-white/$1');
    content = content.replace(/border-white(?![\w\-\/])/g, 'border-black dark:border-white');
    
    // Text colors
    // Only replace text-white when it doesn't already have dark: prefix and isn't part of another class like text-white/50
    content = content.replace(/(?<!dark:)text-white(?!\/)(?!-)/g, 'text-black dark:text-white');
    content = content.replace(/(?<!dark:)(!?)text-white\/(\d+)/g, '$1text-black/$2 dark:$1text-white/$2');

    // Backgrounds opacity (e.g. bg-black/50 -> bg-white/50 dark:bg-black/50)
    content = content.replace(/(?<!dark:)bg-black\/(\d+)/g, 'bg-black/10 dark:bg-black/$1'); // keeping backdrop for light but lighter
    content = content.replace(/(?<!dark:)bg-white\/(\d+)/g, 'bg-gray-100 dark:bg-white/$1'); 

    if (content !== originalContent) {
        fs.writeFileSync(file, content, 'utf8');
        changedFiles++;
    }
});

console.log(`Updated ${changedFiles} files to support light mode tailwind classes.`);
