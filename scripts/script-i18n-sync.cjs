const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

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

let keysMap = new Map(); // key -> default text (if any)

// Extract from JS
files.filter(f => f.endsWith('.js')).forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    const regex1 = /window\.i18n\.t\('([^']+)',\s*'([^']+)'\)/g;
    const regex2 = /window\.i18n\.t\("([^"]+)",\s*"([^"]+)"\)/g;
    const regex3 = /window\.i18n\.t\('([^']+)',\s*"([^"]+)"\)/g;
    const regex4 = /window\.i18n\.t\("([^"]+)",\s*'([^']+)'\)/g;

    [regex1, regex2, regex3, regex4].forEach(regex => {
        let match;
        while ((match = regex.exec(content)) !== null) {
            keysMap.set(match[1], match[2]);
        }
    });
});

// Extract from PHP (Blade)
files.filter(f => f.endsWith('.php')).forEach(file => {
    const content = fs.readFileSync(file, 'utf8');
    
    // Using regex to find data-i18n="..."
    // Then we try to find its text content. It's tricky with regex, so let's parse HTML.
    // For JS, it's easier, but for Blade templates JSDOM might throw errors if there are PHP tags.
    // We can pre-process by stripping blade tags or just regex.
    const keyRegex = /data-i18n="([^"]+)"([^>]*)>(.*?)</gs;
    let match;
    while ((match = keyRegex.exec(content)) !== null) {
        const key = match[1];
        const text = match[3].trim();
        // Avoid adding keys that contain blade logic
        if (!text.includes('{{') && !text.includes('@') && text.length > 0) {
            keysMap.set(key, text);
        } else if (!keysMap.has(key)) {
            keysMap.set(key, "");
        }
    }

    // Attributes like placeholder
    const attrRegexes = [
        { re: /data-i18n-placeholder="([^"]+)"([^>]*)placeholder="([^"]+)"/gs, keyIdx: 1, valIdx: 3 },
        { re: /data-i18n-title="([^"]+)"([^>]*)title="([^"]+)"/gs, keyIdx: 1, valIdx: 3 },
        { re: /data-i18n-tooltip="([^"]+)"([^>]*)data-tooltip="([^"]+)"/gs, keyIdx: 1, valIdx: 3 },
        { re: /data-i18n-aria="([^"]+)"([^>]*)aria-label="([^"]+)"/gs, keyIdx: 1, valIdx: 3 },
    ];
    attrRegexes.forEach(({re, keyIdx, valIdx}) => {
        let m;
        while ((m = re.exec(content)) !== null) {
            keysMap.set(m[keyIdx], m[valIdx]);
        }
    });
});

const enPath = path.join(__dirname, 'resources', 'lang', 'en.json');
const idPath = path.join(__dirname, 'resources', 'lang', 'id.json');

const enDict = JSON.parse(fs.readFileSync(enPath, 'utf8'));
const idDict = JSON.parse(fs.readFileSync(idPath, 'utf8'));

let addedId = 0;

for (const [key, val] of keysMap.entries()) {
    if (!idDict[key] || idDict[key] === "") {
        if (val) {
            idDict[key] = val;
            addedId++;
        }
    }
}

// Write only if it has a value, or remove empty keys
for (let key in enDict) {
    if (enDict[key] === "") {
        delete enDict[key];
    }
}
for (let key in idDict) {
    if (idDict[key] === "") {
        delete idDict[key];
    }
}

fs.writeFileSync(enPath, JSON.stringify(enDict, null, 2));
fs.writeFileSync(idPath, JSON.stringify(idDict, null, 2));

console.log(`Extracted default texts. Added/Updated ${addedId} keys in id.json with real text.`);
