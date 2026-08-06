import dictionaryData from '../lang/dictionary.json';

class PageTranslator {
    constructor() {
        // Flatten the dictionary for runtime use
        const flatDict = {};
        for (const [key, val] of Object.entries(dictionaryData)) {
            if (val && val.en) {
                flatDict[key] = val.en;
            }
        }
        
        this.dictionary = this.sortDictionary(flatDict);
        this.originalNodes = new WeakMap(); // Map: DOM Node -> Original Value (String)
        this.activeLang = localStorage.getItem('app_language') || 'id';
        
        if (!['id', 'en'].includes(this.activeLang)) {
            this.activeLang = 'id';
        }
        
        this.observer = null;
        this.skipElements = new Set(['SCRIPT', 'STYLE', 'CODE', 'PRE', 'TEXTAREA', 'INPUT', 'SVG', 'CANVAS', 'OPTION', 'NOSCRIPT']);
        this.attributesToTranslate = ['placeholder', 'title', 'aria-label', 'alt', 'aria-description'];
        
        // Stats
        this.stats = {
            totalEntries: Object.keys(this.dictionary).length,
            translated: 0,
            skipped: 0,
            ignored: 0
        };

        this.debug = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.includes('.test') || window.location.hostname === '';
        this.missingTranslations = new Set();
    }

    sortDictionary(dict) {
        // Sort dictionary by: 1. Longest phrase, 2. Phrase with punctuation, 3. Single word
        const entries = Object.entries(dict);
        entries.sort((a, b) => {
            const strA = a[0];
            const strB = b[0];
            if (strA.length !== strB.length) {
                return strB.length - strA.length;
            }
            const hasPuncA = /[.,!?]/.test(strA) ? 1 : 0;
            const hasPuncB = /[.,!?]/.test(strB) ? 1 : 0;
            if (hasPuncA !== hasPuncB) {
                return hasPuncB - hasPuncA;
            }
            return 0;
        });
        
        const sorted = {};
        for (const [k, v] of entries) {
            sorted[k] = v;
        }
        return sorted;
    }

    start() {
        this.translateDOM(document.body);
        this.startObserver();
        this.printStats();
        this.updateLanguageButtons();
    }

    startObserver() {
        this.observer = new MutationObserver(mutations => {
            let shouldPrintStats = false;
            if (this.observer) this.observer.disconnect();
            
            mutations.forEach(mutation => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            this.translateDOM(node);
                            shouldPrintStats = true;
                        } else if (node.nodeType === Node.TEXT_NODE) {
                            this.processTextNode(node);
                            shouldPrintStats = true;
                        }
                    });
                } else if (mutation.type === 'attributes') {
                    this.processAttributes(mutation.target);
                    shouldPrintStats = true;
                }
            });
            
            if (shouldPrintStats && this.debug) {
                this.printStats();
            }
            
            this.resumeObserver();
        });

        this.resumeObserver();
    }

    resumeObserver() {
        if (this.observer) {
            this.observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: this.attributesToTranslate.concat(['value'])
            });
        }
    }

    async setLanguage(lang, animate = true) {
        if (!['id', 'en'].includes(lang)) return;
        if (this.activeLang === lang) return;

        this.activeLang = lang;
        localStorage.setItem('app_language', lang);

        if (animate && document.body) {
            document.body.classList.add('i18n-fade-out');
            await new Promise(resolve => setTimeout(resolve, 150));
        }

        // Reset stats for full DOM re-evaluation
        this.stats.translated = 0;
        this.stats.skipped = 0;
        this.stats.ignored = 0;

        if (this.observer) this.observer.disconnect();
        
        this.translateDOM(document.body);
        this.updateLanguageButtons();

        if (this.observer) this.resumeObserver();

        if (animate && document.body) {
            document.body.classList.remove('i18n-fade-out');
        }

        this.printStats();
    }

    translateDOM(root) {
        if (!root || root.closest && root.closest('[data-no-translate]')) {
            this.stats.ignored++;
            return;
        }

        const walker = document.createTreeWalker(
            root,
            NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT,
            {
                acceptNode: (node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const tag = node.tagName.toUpperCase();
                        if (this.skipElements.has(tag) || node.hasAttribute('data-no-translate')) {
                            this.stats.ignored++;
                            return NodeFilter.FILTER_REJECT;
                        }
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );

        let node;
        const nodesToProcess = [];
        const attrsToProcess = [];

        while (node = walker.nextNode()) {
            if (node.nodeType === Node.TEXT_NODE) {
                nodesToProcess.push(node);
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                attrsToProcess.push(node);
            }
        }

        nodesToProcess.forEach(n => this.processTextNode(n));
        attrsToProcess.forEach(n => this.processAttributes(n));
        
        if (root.nodeType === Node.ELEMENT_NODE && !this.skipElements.has(root.tagName.toUpperCase()) && !root.hasAttribute('data-no-translate')) {
            this.processAttributes(root);
        }
    }

    escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    replacePhrase(originalText, searchPhrase, replacement) {
        const escaped = this.escapeRegExp(searchPhrase);
        // Match phrase surrounded by start/end of string, or non-alphanumeric (allowing spaces, punctuation, brackets)
        // A-Za-z0-9_À-ÿ captures letters/numbers.
        const regex = new RegExp(`(^|[^a-zA-Z0-9_À-ÿ])(${escaped})([^a-zA-Z0-9_À-ÿ]|$)`, 'gi');
        return originalText.replace(regex, (match, p1, p2, p3) => p1 + replacement + p3);
    }

    processTextNode(textNode) {
        if (!textNode.nodeValue || !textNode.nodeValue.trim()) return;

        const parent = textNode.parentElement;
        if (parent && (this.skipElements.has(parent.tagName.toUpperCase()) || parent.closest('[data-no-translate]'))) {
            this.stats.ignored++;
            return;
        }

        if (!this.originalNodes.has(textNode)) {
            this.originalNodes.set(textNode, textNode.nodeValue);
        }

        const originalText = this.originalNodes.get(textNode);

        if (this.activeLang === 'en') {
            let processedText = originalText;
            let wasTranslated = false;
            
            // Loop through dictionary (already sorted by longest first)
            for (const [idPhrase, enPhrase] of Object.entries(this.dictionary)) {
                if (processedText.toLowerCase().includes(idPhrase.toLowerCase())) {
                    const newText = this.replacePhrase(processedText, idPhrase, enPhrase);
                    if (newText !== processedText) {
                        processedText = newText;
                        wasTranslated = true;
                    }
                }
            }
            
            if (wasTranslated && textNode.nodeValue !== processedText) {
                textNode.nodeValue = processedText;
                this.stats.translated++;
            } else if (!wasTranslated) {
                // If it wasn't translated by any dictionary item, maybe it's just plain text we missed.
                this.logMissing(originalText.trim());
                this.stats.skipped++;
            }
        } else {
            if (textNode.nodeValue !== originalText) {
                textNode.nodeValue = originalText;
                this.stats.translated++;
            }
        }
    }

    processAttributes(element) {
        this.attributesToTranslate.forEach(attr => {
            if (element.hasAttribute(attr)) {
                this.translateAttribute(element, attr);
            }
        });

        if (element.tagName === 'INPUT' && ['button', 'submit', 'reset'].includes(element.type.toLowerCase())) {
            this.translateAttribute(element, 'value');
        }
    }

    translateAttribute(element, attrName) {
        const attrNode = element.getAttributeNode(attrName);
        if (!attrNode || !attrNode.value.trim()) return;
        
        if (!this.originalNodes.has(attrNode)) {
            this.originalNodes.set(attrNode, attrNode.value);
        }

        const originalText = this.originalNodes.get(attrNode);

        if (this.activeLang === 'en') {
            let processedText = originalText;
            let wasTranslated = false;
            
            for (const [idPhrase, enPhrase] of Object.entries(this.dictionary)) {
                if (processedText.toLowerCase().includes(idPhrase.toLowerCase())) {
                    const newText = this.replacePhrase(processedText, idPhrase, enPhrase);
                    if (newText !== processedText) {
                        processedText = newText;
                        wasTranslated = true;
                    }
                }
            }
            
            if (wasTranslated && attrNode.value !== processedText) {
                attrNode.value = processedText;
                this.stats.translated++;
            } else if (!wasTranslated) {
                this.logMissing(originalText.trim());
                this.stats.skipped++;
            }
        } else {
            if (attrNode.value !== originalText) {
                attrNode.value = originalText;
                this.stats.translated++;
            }
        }
    }

    logMissing(phrase) {
        if (!this.debug) return;
        
        // Skip purely numeric/symbol strings from logging
        if (/^[\d\s\W]+$/.test(phrase) || !phrase) return;
        
        if (!this.missingTranslations.has(phrase)) {
            this.missingTranslations.add(phrase);
            
            const url = window.location.pathname;
            
            // Count approximate occurrences
            let count = 0;
            const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
            let n;
            while (n = walker.nextNode()) {
                if (n.nodeValue.includes(phrase)) count++;
            }
            
            console.warn(`[Translator] Missing Translation\n"${phrase}"\n\nPage: ${url}\nOccurrences: ${count}\n\nSuggested:\n"${phrase}": {\n    "en": ""\n}`);
        }
    }

    printStats() {
        if (!this.debug) return;
        console.groupCollapsed(`[Translator] Translation Statistics (${this.activeLang})`);
        console.log(`Total Dictionary Entries: ${this.stats.totalEntries}`);
        console.log(`Translated Nodes: ${this.stats.translated}`);
        console.log(`Skipped Nodes: ${this.stats.skipped}`);
        console.log(`Ignored Nodes: ${this.stats.ignored}`);
        console.groupEnd();
    }

    updateLanguageButtons() {
        const buttons = document.querySelectorAll('[data-lang-btn]');
        buttons.forEach(btn => {
            const lang = btn.getAttribute('data-lang-btn');
            if (lang === this.activeLang) {
                btn.classList.add('is-active');
                btn.setAttribute('aria-current', 'true');
            } else {
                btn.classList.remove('is-active');
                btn.removeAttribute('aria-current');
            }
        });
    }
}

export const pageTranslator = new PageTranslator();
window.pageTranslator = pageTranslator;
