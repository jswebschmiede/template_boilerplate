#!/usr/bin/env node

/* eslint-env node */

/**
 * Replaces boilerplate template placeholders with a project-specific slug
 * and renames files whose names contain "boilerplate" (e.g. language INIs).
 */

const { existsSync, readdirSync, readFileSync, renameSync, statSync, writeFileSync } = require('fs');
const { basename, dirname, extname, join, relative } = require('path');

const rootDir = join(__dirname, '..');
const scriptRelativePath = 'scripts/rename-template.js';
const args = process.argv.slice(2);
const isDryRun = args.includes('--dry-run');
const slugArg = args.find((arg) => !arg.startsWith('--'));

const usageMessage = 'Usage: node scripts/rename-template.js <slug> [--dry-run]';

const allowedExtensions = new Set(['.php', '.xml', '.ini', '.json', '.js', '.css', '.md']);

const skippedDirectories = new Set(['node_modules', '.git', 'dist', 'vendor']);

/**
 * Converts a slug to PascalCase for PHP namespaces.
 *
 * @param {string} value Slug with hyphens and/or underscores
 * @returns {string} PascalCase namespace segment
 */
function slugToNamespace(value) {
    return value
        .split(/[-_]/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

/**
 * Converts a slug to a human-readable title.
 *
 * @param {string} value Slug with hyphens and/or underscores
 * @returns {string} Title with spaces
 */
function slugToTitle(value) {
    return value
        .split(/[-_]/)
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

/**
 * Validates the requested template slug.
 *
 * @param {string | undefined} value Requested slug
 * @returns {void}
 */
function validateSlug(value) {
    if (!value) {
        console.error(usageMessage);
        process.exit(1);
    }

    if (!/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/.test(value)) {
        console.error('Slug must contain only lowercase letters, numbers, hyphens, and underscores.');
        console.error(usageMessage);
        process.exit(1);
    }
}

/**
 * Builds the replacement map from longest boilerplate tokens to the new slug forms.
 *
 * @param {string} templateName Normalized template name (hyphens become underscores)
 * @returns {Record<string, string>} Replacement map
 */
function getReplacements(templateName) {
    const pascal = slugToNamespace(templateName);
    const constant = templateName.toUpperCase();
    const title = slugToTitle(templateName);

    return {
        TPL_TEMPLATE_BOILERPLATE: `TPL_${constant}`,
        TEMPLATE_BOILERPLATE: constant,
        tpl_template_boilerplate: `tpl_${templateName}`,
        template_boilerplate: templateName,
        'template-boilerplate': templateName,
        'Template Boilerplate': title,
        'template.boilerplate': `template.${templateName}`,
        BOILERPLATE: constant,
        Boilerplate: pascal,
        boilerplate: templateName,
    };
}

/**
 * Applies all replacements to a string in a stable order (longer keys first).
 *
 * @param {string} content Original content
 * @param {Record<string, string>} replacements Replacement map
 * @returns {string} Updated content
 */
function applyReplacements(content, replacements) {
    const orderedEntries = Object.entries(replacements).sort(
        ([searchA], [searchB]) => searchB.length - searchA.length
    );

    return orderedEntries.reduce(
        (updatedContent, [search, replacement]) => updatedContent.replaceAll(search, replacement),
        content
    );
}

/**
 * Determines whether a path should be skipped.
 *
 * @param {string} absolutePath Absolute file or directory path
 * @param {string} relativePath POSIX-style path from the project root
 * @returns {boolean} True when the path should be ignored
 */
function shouldSkipPath(absolutePath, relativePath) {
    if (relativePath === scriptRelativePath) {
        return true;
    }

    const baseName = basename(absolutePath);

    return skippedDirectories.has(baseName);
}

/**
 * Recursively collects files under the project root.
 *
 * @param {string} directory Directory to scan
 * @param {string} relativeBase Relative path from the project root
 * @returns {string[]} Absolute file paths
 */
function collectFiles(directory, relativeBase = '') {
    if (!existsSync(directory)) {
        return [];
    }

    const files = [];

    for (const entry of readdirSync(directory)) {
        const absolutePath = join(directory, entry);
        const relativePath = relativeBase ? `${relativeBase}/${entry}` : entry;

        if (shouldSkipPath(absolutePath, relativePath)) {
            continue;
        }

        const stats = statSync(absolutePath);

        if (stats.isDirectory()) {
            files.push(...collectFiles(absolutePath, relativePath));
            continue;
        }

        if (stats.isFile()) {
            files.push(absolutePath);
        }
    }

    return files;
}

validateSlug(slugArg);

if (slugArg === 'boilerplate' || slugArg === 'template_boilerplate' || slugArg === 'template-boilerplate') {
    console.error('New slug must differ from the boilerplate placeholders.');
    process.exit(1);
}

const templateName = slugArg.replaceAll('-', '_');
const replacements = getReplacements(templateName);
const files = collectFiles(rootDir);
const changedFiles = [];
const renamedFiles = [];

console.log('Template rename values:');
console.log(`Template name: ${templateName}`);
console.log(`PHP namespace: JSch\\Template\\${replacements.Boilerplate}`);
console.log(`Language prefix: TPL_${templateName.toUpperCase()}`);
console.log(`WebAsset: template.${templateName}.app`);
console.log(`Display title: ${replacements['Template Boilerplate']}`);

for (const file of files) {
    if (!allowedExtensions.has(extname(file))) {
        continue;
    }

    const originalContent = readFileSync(file, 'utf8');
    const updatedContent = applyReplacements(originalContent, replacements);

    if (originalContent === updatedContent) {
        continue;
    }

    changedFiles.push(file);

    if (!isDryRun) {
        writeFileSync(file, updatedContent, 'utf8');
    }
}

for (const file of files) {
    const oldName = basename(file);
    const newName = applyReplacements(oldName, replacements);

    if (oldName === newName || !/boilerplate/i.test(oldName)) {
        continue;
    }

    const newPath = join(dirname(file), newName);

    if (existsSync(newPath) && newPath !== file) {
        console.error(`Target file already exists: ${relative(rootDir, newPath)}`);
        process.exit(1);
    }

    renamedFiles.push({ from: file, to: newPath });

    if (!isDryRun) {
        renameSync(file, newPath);
    }
}

if (isDryRun) {
    console.log('Dry run only. No files were changed.');
}

console.log(`Affected files: ${changedFiles.length}`);
for (const file of changedFiles) {
    console.log(relative(rootDir, file));
}

if (renamedFiles.length > 0) {
    console.log(`Renamed files: ${renamedFiles.length}`);
    for (const { from, to } of renamedFiles) {
        console.log(`${relative(rootDir, from)} -> ${relative(rootDir, to)}`);
    }
}

console.log('');
console.log('Next steps:');
console.log(`- Rename the project folder template_boilerplate -> ${templateName}`);
console.log('- Update git remotes or workspace paths if needed');
