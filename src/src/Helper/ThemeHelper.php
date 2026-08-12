<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.template_boilerplate
 *
 * @copyright   (C) 2024. All rights reserved by Jörg Schöneburg
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

declare(strict_types=1);

namespace JSch\Template\Boilerplate\Site\Helper;

use Joomla\CMS\Log\Log;
use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Oklch;
use Throwable;

/**
 * Parses theme.json and builds :root CSS custom properties for the template.
 *
 * Tokens under settings are mapped dynamically to
 * --tpl--style--global--{category}--{slug} without a fixed key allowlist.
 */
class ThemeHelper
{
    private const CSS_PREFIX = '--tpl--style--global--';

    /**
     * Request-local memoization of generated CSS keyed by template name.
     *
     * @var array<string, string>
     */
    private static array $cssCache = [];

    /**
     * Whether the Composer autoloader has been loaded for this request.
     */
    private static bool $autoloadLoaded = false;

    /**
     * Builds :root CSS custom properties from the template theme.json.
     *
     * @param string $template Template name (folder under JPATH_THEMES)
     *
     * @return string CSS string for addInlineStyle, or empty string on failure
     */
    public static function buildRootStyles(string $template): string
    {
        if (isset(self::$cssCache[$template])) {
            return self::$cssCache[$template];
        }

        $themePath = JPATH_THEMES . '/' . $template . '/theme.json';

        if (!is_readable($themePath)) {
            Log::add(
                sprintf('theme.json not readable for template "%s" at %s', $template, $themePath),
                Log::WARNING,
                'template_boilerplate'
            );
            self::$cssCache[$template] = '';

            return '';
        }

        $raw = file_get_contents($themePath);

        if ($raw === false) {
            Log::add(
                sprintf('Unable to read theme.json for template "%s"', $template),
                Log::WARNING,
                'template_boilerplate'
            );
            self::$cssCache[$template] = '';

            return '';
        }

        $data = json_decode($raw, true);

        if (!is_array($data)) {
            Log::add(
                sprintf('Invalid theme.json JSON for template "%s"', $template),
                Log::WARNING,
                'template_boilerplate'
            );
            self::$cssCache[$template] = '';

            return '';
        }

        self::loadComposerAutoload($template);

        $settings = $data['settings'] ?? null;

        if (!is_array($settings)) {
            self::$cssCache[$template] = '';

            return '';
        }

        $declarations = self::collectDeclarations($settings, $template);

        if ($declarations === []) {
            self::$cssCache[$template] = '';

            return '';
        }

        $css = ':root{' . implode('', $declarations) . '}';
        self::$cssCache[$template] = $css;

        return $css;
    }

    /**
     * Collects CSS declarations from the settings object.
     *
     * Supported shapes (all keys are open-ended):
     * - settings.color.{slug}
     * - settings.typography.{group}.{slug}  (fontFamily → font-family, …)
     * - settings.custom.{group}.{slug}
     * - settings.{group}.{slug}             (flat scalar map)
     * - settings.{group}.{sub}.{slug}       (nested like custom)
     *
     * @param array<string, mixed> $settings Theme settings
     * @param string               $template Template name
     *
     * @return list<string>
     */
    private static function collectDeclarations(array $settings, string $template): array
    {
        $declarations = [];

        foreach ($settings as $section => $sectionValue) {
            if (!is_string($section) || $section === '' || !is_array($sectionValue)) {
                continue;
            }

            $sectionSlug = self::toKebabCase($section);

            if ($sectionSlug === 'color') {
                self::appendScalarMap(
                    $declarations,
                    $sectionValue,
                    'color',
                    $template,
                    true
                );
                continue;
            }

            if ($sectionSlug === 'typography' || $sectionSlug === 'custom') {
                self::appendGroupedMap($declarations, $sectionValue, $template, false);
                continue;
            }

            if (self::isScalarMap($sectionValue)) {
                self::appendScalarMap(
                    $declarations,
                    $sectionValue,
                    $sectionSlug,
                    $template,
                    false
                );
                continue;
            }

            self::appendGroupedMap($declarations, $sectionValue, $template, false);
        }

        return $declarations;
    }

    /**
     * Appends declarations for a flat slug → scalar map.
     *
     * @param list<string>         $declarations Collected CSS declarations
     * @param array<string, mixed> $map          Slug → value map
     * @param string               $category     CSS category segment
     * @param string               $template     Template name
     * @param bool                 $asColor      Whether values are colors
     *
     * @return void
     */
    private static function appendScalarMap(
        array &$declarations,
        array $map,
        string $category,
        string $template,
        bool $asColor
    ): void {
        foreach ($map as $slug => $value) {
            if (!is_string($slug) || $slug === '' || !is_scalar($value)) {
                if (JDEBUG && !is_scalar($value) && $value !== null) {
                    Log::add(
                        sprintf('theme.json settings.%s.%s is not a scalar value', $category, (string) $slug),
                        Log::WARNING,
                        'template_boilerplate'
                    );
                }

                continue;
            }

            $stringValue = trim((string) $value);

            if ($stringValue === '') {
                continue;
            }

            if ($asColor) {
                $normalized = self::normalizeColor($stringValue, $template);

                if ($normalized === null) {
                    continue;
                }

                $stringValue = $normalized;
            }

            $cssVar = self::CSS_PREFIX . $category . '--' . self::toKebabCase($slug);
            $declarations[] = sprintf('%s: %s;', $cssVar, $stringValue);
        }
    }

    /**
     * Appends declarations for nested group → slug → scalar maps.
     *
     * @param list<string>         $declarations Collected CSS declarations
     * @param array<string, mixed> $groups       Group → map
     * @param string               $template     Template name
     * @param bool                 $asColor      Whether nested values are colors
     *
     * @return void
     */
    private static function appendGroupedMap(
        array &$declarations,
        array $groups,
        string $template,
        bool $asColor
    ): void {
        foreach ($groups as $group => $map) {
            if (!is_string($group) || $group === '' || !is_array($map)) {
                continue;
            }

            self::appendScalarMap(
                $declarations,
                $map,
                self::toKebabCase($group),
                $template,
                $asColor
            );
        }
    }

    /**
     * Checks whether an array is a flat map of scalar values.
     *
     * @param array<mixed> $map Candidate map
     *
     * @return bool
     */
    private static function isScalarMap(array $map): bool
    {
        if ($map === []) {
            return false;
        }

        foreach ($map as $value) {
            if (!is_scalar($value) && $value !== null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Converts camelCase / mixed keys to kebab-case CSS segments.
     *
     * @param string $value Raw key
     *
     * @return string
     */
    private static function toKebabCase(string $value): string
    {
        $value = preg_replace('/([a-z0-9])([A-Z])/', '$1-$2', $value) ?? $value;
        $value = str_replace(['_', ' '], '-', $value);
        $value = strtolower($value);

        return preg_replace('/-+/', '-', $value) ?? $value;
    }

    /**
     * Loads the template Composer autoloader when available.
     *
     * @param string $template Template name
     *
     * @return void
     */
    private static function loadComposerAutoload(string $template): void
    {
        if (self::$autoloadLoaded) {
            return;
        }

        $autoload = JPATH_THEMES . '/' . $template . '/vendor/autoload.php';

        if (is_file($autoload)) {
            require_once $autoload;
        } else {
            Log::add(
                sprintf('Composer vendor/autoload.php missing for template "%s"', $template),
                Log::WARNING,
                'template_boilerplate'
            );
        }

        self::$autoloadLoaded = true;
    }

    /**
     * Normalizes a color string to oklch() for CSS output.
     *
     * Hex values are converted via Iris; existing oklch() strings pass through.
     *
     * @param string $value    Color value from theme.json
     * @param string $template Template name (for logging context)
     *
     * @return string|null Normalized oklch CSS value, or null on failure
     */
    private static function normalizeColor(string $value, string $template): ?string
    {
        if (preg_match('/^oklch\s*\(/i', $value) === 1) {
            return $value;
        }

        if (!class_exists(Factory::class)) {
            Log::add(
                sprintf(
                    'Cannot convert hex color "%s" for template "%s": Iris library not loaded',
                    $value,
                    $template
                ),
                Log::WARNING,
                'template_boilerplate'
            );

            return null;
        }

        try {
            $color = Factory::init($value);

            if ($color instanceof Oklch) {
                return (string) $color;
            }

            return (string) $color->toOklch();
        } catch (Throwable $exception) {
            Log::add(
                sprintf(
                    'Invalid color "%s" in theme.json for template "%s": %s',
                    $value,
                    $template,
                    $exception->getMessage()
                ),
                Log::WARNING,
                'template_boilerplate'
            );

            return null;
        }
    }
}
