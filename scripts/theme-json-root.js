/* eslint-env node */

/**
 * PostCSS plugin: inject :root CSS custom properties from theme.json
 * into the TinyMCE editor.css entry (mirrors ThemeHelper.php).
 */

const fs = require('fs');
const path = require('path');
const { Rule, Declaration } = require('postcss');
const { parse, converter } = require('culori');

const PLUGIN_NAME = 'theme-json-root';
const CSS_PREFIX = '--tpl--style--global--';
const toOklch = converter('oklch');

/**
 * Converts camelCase / mixed keys to kebab-case CSS segments.
 * @param {string} value Raw key
 * @returns {string}
 */
function toKebabCase(value) {
    let result = value.replace(/([a-z0-9])([A-Z])/g, '$1-$2');
    result = result.replace(/[_ ]+/g, '-');
    result = result.toLowerCase();
    return result.replace(/-+/g, '-');
}

/**
 * Checks whether an object is a flat map of scalar values.
 * @param {Record<string, unknown>} map Candidate map
 * @returns {boolean}
 */
function isScalarMap(map) {
    const values = Object.values(map);

    if (values.length === 0) {
        return false;
    }

    return values.every((value) => value === null || typeof value !== 'object');
}

/**
 * Normalizes a color string to oklch() (Iris-compatible L% C H format).
 * Existing oklch() strings pass through unchanged.
 * @param {string} value Color value from theme.json
 * @returns {string|null}
 */
function normalizeColor(value) {
    if (/^oklch\s*\(/i.test(value)) {
        return value;
    }

    const parsed = parse(value);

    if (!parsed) {
        console.warn(`[${PLUGIN_NAME}] Invalid color "${value}" in theme.json — skipped`);
        return null;
    }

    const oklch = toOklch(parsed);

    if (!oklch || typeof oklch.l !== 'number') {
        console.warn(`[${PLUGIN_NAME}] Cannot convert color "${value}" to oklch — skipped`);
        return null;
    }

    const l = (oklch.l * 100).toFixed(2);
    const c = (oklch.c ?? 0).toFixed(4);
    const h = (oklch.h ?? 0).toFixed(2);

    return `oklch(${l}% ${c} ${h})`;
}

/**
 * Appends declarations for a flat slug → scalar map.
 * @param {{ prop: string, value: string }[]} declarations Collected CSS declarations
 * @param {Record<string, unknown>} map Slug → value map
 * @param {string} category CSS category segment
 * @param {boolean} asColor Whether values are colors
 * @returns {void}
 */
function appendScalarMap(declarations, map, category, asColor) {
    for (const [slug, value] of Object.entries(map)) {
        if (typeof slug !== 'string' || slug === '' || (typeof value === 'object' && value !== null)) {
            continue;
        }

        if (value === null || value === undefined) {
            continue;
        }

        let stringValue = String(value).trim();

        if (stringValue === '') {
            continue;
        }

        if (asColor) {
            const normalized = normalizeColor(stringValue);

            if (normalized === null) {
                continue;
            }

            stringValue = normalized;
        }

        declarations.push({
            prop: `${CSS_PREFIX}${category}--${toKebabCase(slug)}`,
            value: stringValue,
        });
    }
}

/**
 * Appends declarations for nested group → slug → scalar maps.
 * @param {{ prop: string, value: string }[]} declarations Collected CSS declarations
 * @param {Record<string, unknown>} groups Group → map
 * @param {boolean} asColor Whether nested values are colors
 * @returns {void}
 */
function appendGroupedMap(declarations, groups, asColor) {
    for (const [group, map] of Object.entries(groups)) {
        if (typeof group !== 'string' || group === '' || typeof map !== 'object' || map === null || Array.isArray(map)) {
            continue;
        }

        appendScalarMap(declarations, map, toKebabCase(group), asColor);
    }
}

/**
 * Collects CSS declarations from theme.json settings (same shapes as ThemeHelper).
 * @param {Record<string, unknown>} settings Theme settings
 * @returns {{ prop: string, value: string }[]}
 */
function collectDeclarations(settings) {
    const declarations = [];

    for (const [section, sectionValue] of Object.entries(settings)) {
        if (
            typeof section !== 'string' ||
            section === '' ||
            typeof sectionValue !== 'object' ||
            sectionValue === null ||
            Array.isArray(sectionValue)
        ) {
            continue;
        }

        const sectionSlug = toKebabCase(section);

        if (sectionSlug === 'color') {
            appendScalarMap(declarations, sectionValue, 'color', true);
            continue;
        }

        if (sectionSlug === 'typography' || sectionSlug === 'custom') {
            appendGroupedMap(declarations, sectionValue, false);
            continue;
        }

        if (isScalarMap(sectionValue)) {
            appendScalarMap(declarations, sectionValue, sectionSlug, false);
            continue;
        }

        appendGroupedMap(declarations, sectionValue, false);
    }

    return declarations;
}

/**
 * Reads theme.json and returns CSS custom property declarations.
 * @param {string} themePath Absolute path to theme.json
 * @returns {{ prop: string, value: string }[]|null}
 */
function buildRootDeclarations(themePath) {
    if (!fs.existsSync(themePath)) {
        console.warn(`[${PLUGIN_NAME}] theme.json not found at ${themePath}`);
        return null;
    }

    let data;

    try {
        data = JSON.parse(fs.readFileSync(themePath, 'utf8'));
    } catch (error) {
        console.warn(`[${PLUGIN_NAME}] Invalid theme.json JSON: ${error.message}`);
        return null;
    }

    const settings = data?.settings;

    if (typeof settings !== 'object' || settings === null || Array.isArray(settings)) {
        return null;
    }

    const declarations = collectDeclarations(settings);

    if (declarations.length === 0) {
        return null;
    }

    return declarations;
}

/**
 * @type {import('postcss').PluginCreator}
 */
const themeJsonRoot = () => {
    const themePath = path.resolve(__dirname, '../src/theme.json');

    return {
        postcssPlugin: PLUGIN_NAME,
        Once(root, { result }) {
            const from = result.opts.from || '';

            result.messages.push({
                type: 'dependency',
                plugin: PLUGIN_NAME,
                file: themePath,
                parent: from,
            });

            if (path.basename(from) !== 'editor.css') {
                return;
            }

            if (from.includes(`${path.sep}vendor${path.sep}`)) {
                return;
            }

            const declarations = buildRootDeclarations(themePath);

            if (declarations === null) {
                return;
            }

            const rootRule = new Rule({ selector: ':root' });

            for (const { prop, value } of declarations) {
                rootRule.append(new Declaration({ prop, value }));
            }

            root.prepend(rootRule);
        },
    };
};

themeJsonRoot.postcss = true;

module.exports = themeJsonRoot;
