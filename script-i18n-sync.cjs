const fs = require('fs');
const path = require('path');

function getFiles(dir, filesList = []) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filePath = path.join(dir, file);
        if (fs.statSync(filePath).isDirectory()) {
            getFiles(filePath, filesList);
        } else {
            if (filePath.endsWith('.php') || filePath.endsWith('.js')) {
                filesList.push(filePath);
            }
        }
    }
    return filesList;
}

const viewsDir = path.join(__dirname, 'resources', 'views');
const jsDir = path.join(__dirname, 'resources', 'js');
const files = [...getFiles(viewsDir), ...getFiles(jsDir)];

let keys = new Set();

const regexes = [
    /data-i18n="([^"]+)"/g,
    /data-i18n-placeholder="([^"]+)"/g,
    /data-i18n-title="([^"]+)"/g,
    /data-i18n-tooltip="([^"]+)"/g,
    /data-i18n-aria="([^"]+)"/g,
    /window\.i18n\.t\('([^']+)'/g,
    /window\.i18n\.t\("([^"]+)"/g,
];

files.forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    regexes.forEach(regex => {
        let match;
        while ((match = regex.exec(content)) !== null) {
            keys.add(match[1]);
        }
    });
});

const enPath = path.join(__dirname, 'resources', 'lang', 'en.json');
const idPath = path.join(__dirname, 'resources', 'lang', 'id.json');

const enDict = JSON.parse(fs.readFileSync(enPath, 'utf8'));
const idDict = JSON.parse(fs.readFileSync(idPath, 'utf8'));

let addedEn = 0;
let addedId = 0;

keys.forEach(key => {
    if (!enDict[key]) {
        // We set to empty or placeholder for English
        enDict[key] = idDict[key] || ""; 
        addedEn++;
    }
    if (!idDict[key]) {
        // We leave it empty for ID if not found, but realistically we need ID to be filled.
        idDict[key] = "";
        addedId++;
    }
});

// For missing English translations, if we copied from ID or it's empty, let's keep track of them
// But for now, just write them.
fs.writeFileSync(enPath, JSON.stringify(enDict, null, 2));
fs.writeFileSync(idPath, JSON.stringify(idDict, null, 2));

console.log(`Found ${keys.size} unique keys in views and JS.`);
console.log(`Added ${addedEn} keys to en.json`);
console.log(`Added ${addedId} keys to id.json`);
