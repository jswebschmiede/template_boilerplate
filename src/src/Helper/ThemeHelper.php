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

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory as CmsFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use OzdemirBurak\Iris\Color\Factory;
use OzdemirBurak\Iris\Color\Oklch;
use Throwable;

/**
 * Template helpers for CSS custom properties and shared front-end data.
 *
 * Tokens under theme.json settings are mapped dynamically to
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
     * Request-local administrator/editor user resolved from site or admin session.
     */
    private static ?User $backendEditorUser = null;

    /**
     * Whether the administrator/editor user has been resolved for this request.
     */
    private static bool $backendEditorUserResolved = false;

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
     * Returns the logged-in user who may reach the administrator article editor.
     *
     * Uses the site identity when present, otherwise an active administrator session cookie.
     *
     * @return User|null Authenticated user, or null when no backend editor session exists
     */
    public static function getBackendEditorUser(): ?User
    {
        if (self::$backendEditorUserResolved) {
            return self::$backendEditorUser;
        }

        self::$backendEditorUserResolved = true;

        $app  = CmsFactory::getApplication();
        $user = $app->getIdentity();

        if ($user instanceof User && !$user->guest) {
            self::$backendEditorUser = $user;

            return self::$backendEditorUser;
        }

        $config    = $app->getConfig();
        $sessionId = self::getAdministratorSessionId($app);

        if ($sessionId === '') {
            return null;
        }

        $lifetime  = ((int) $config->get('lifetime', 15)) * 60;
        $threshold = time() - ($lifetime > 0 ? $lifetime : 900);
        $clientId  = 1;

        try {
            $db    = CmsFactory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select($db->quoteName('userid'))
                ->from($db->quoteName('#__session'))
                ->where($db->quoteName('session_id') . ' = :sessionId')
                ->where($db->quoteName('guest') . ' = 0')
                ->where($db->quoteName('userid') . ' > 0')
                ->where($db->quoteName('time') . ' > :threshold')
                ->bind(':sessionId', $sessionId)
                ->bind(':threshold', $threshold, ParameterType::INTEGER)
                ->setLimit(1);

            if (!$config->get('shared_session', false)) {
                $query->where($db->quoteName('client_id') . ' = :clientId')
                    ->bind(':clientId', $clientId, ParameterType::INTEGER);
            }

            $db->setQuery($query);
            $userId = (int) $db->loadResult();
        } catch (Throwable) {
            return null;
        }

        if ($userId < 1) {
            return null;
        }

        $editor = CmsFactory::getContainer()->get(UserFactoryInterface::class)->loadUserById($userId);

        if ($editor->guest || (int) $editor->block === 1) {
            return null;
        }

        self::$backendEditorUser = $editor;

        return self::$backendEditorUser;
    }

    /**
     * Whether the frontend admin bar should render for this request.
     *
     * @return bool True when the template option is enabled and an administrator session exists
     */
    public static function shouldShowAdminBar(): bool
    {
        $params = CmsFactory::getApplication()->getTemplate(true)->params;

        if ((int) $params->get('adminBar', 1) !== 1) {
            return false;
        }

        $user = self::getBackendEditorUser();

        return $user !== null && $user->authorise('core.login.admin');
    }

    /**
     * Builds administrator shortcut links for the current frontend request.
     *
     * @return list<array{url: string, label: string, icon: string}>
     */
    public static function getAdminBarLinks(): array
    {
        $user = self::getBackendEditorUser();

        if ($user === null) {
            return [];
        }

        $app    = CmsFactory::getApplication();
        $input  = $app->getInput();
        $option = $input->getCmd('option', '');
        $view   = $input->getCmd('view', '');
        $menu   = $app->getMenu()->getActive();
        $menuId = $menu !== null ? (int) $menu->id : 0;
        $links  = [];

        $links[] = [
            'url'   => Route::link('administrator', 'index.php', false),
            'label' => Text::_('TPL_TEMPLATE_BOILERPLATE_ADMINBAR_BACKEND'),
            'icon'  => 'backend',
        ];

        $articleId = (int) $input->getInt('id', 0);

        if ($option === 'com_content' && $view === 'article' && $articleId > 0 && self::canEditArticle($user, $articleId)) {
            $links[] = [
                'url'   => Route::link(
                    'administrator',
                    'index.php?option=com_content&task=article.edit&id=' . $articleId,
                    false
                ),
                'label' => Text::_('TPL_TEMPLATE_BOILERPLATE_ARTICLE_EDIT'),
                'icon'  => 'article',
            ];
        }

        if ($menuId > 0 && $user->authorise('core.manage', 'com_modules')) {
            $links[] = [
                'url'   => Route::link(
                    'administrator',
                    'index.php?option=com_modules&view=modules&client_id=0&filter[menuitem]=' . $menuId,
                    false
                ),
                'label' => Text::_('TPL_TEMPLATE_BOILERPLATE_ADMINBAR_MODULES'),
                'icon'  => 'modules',
            ];
        }

        if ($menuId > 0) {
            $canEditMenu = $user->authorise('core.edit', 'com_menus.item.' . $menuId)
                || $user->authorise('core.edit', 'com_menus');

            if ($canEditMenu) {
                $links[] = [
                    'url'   => Route::link(
                        'administrator',
                        'index.php?option=com_menus&task=item.edit&id=' . $menuId,
                        false
                    ),
                    'label' => Text::_('TPL_TEMPLATE_BOILERPLATE_ADMINBAR_MENU'),
                    'icon'  => 'menu',
                ];
            }
        }

        return $links;
    }

    /**
     * Reads the administrator session id from the backend session cookie.
     *
     * Joomla 4+ names the cookie md5(md5(secret . session_name)). The site app
     * overwrites session_name to "site", so the administrator cookie must be
     * resolved independently of the current application name.
     *
     * @param object $app Current CMS application
     *
     * @return string Session id, or an empty string when the cookie is absent
     */
    private static function getAdministratorSessionId(object $app): string
    {
        $config = $app->getConfig();
        $secret = (string) $config->get('secret');
        $seeds  = [
            'administrator',
            AdministratorApplication::class,
        ];

        $configuredName = $config->get('session_name');

        if (is_string($configuredName) && $configuredName !== '' && $configuredName !== 'site') {
            array_unshift($seeds, $configuredName);
        }

        $cookieInput = $app->getInput()->cookie;

        foreach (array_unique($seeds) as $seed) {
            $cookieName = md5(md5($secret . $seed));
            $sessionId  = '';

            if (isset($_COOKIE[$cookieName])) {
                $sessionId = (string) $_COOKIE[$cookieName];
            }

            if ($sessionId === '') {
                $sessionId = (string) $cookieInput->getRaw($cookieName, '');
            }

            if ($sessionId !== '') {
                return $sessionId;
            }
        }

        return '';
    }

    /**
     * Whether the user may edit the given article in the administrator.
     *
     * @param User $user      Authenticated backend editor
     * @param int  $articleId Content item id
     *
     * @return bool True when core.edit or core.edit.own (as author) applies
     */
    private static function canEditArticle(User $user, int $articleId): bool
    {
        $asset = 'com_content.article.' . $articleId;

        if ($user->authorise('core.edit', $asset)) {
            return true;
        }

        if (!$user->authorise('core.edit.own', $asset)) {
            return false;
        }

        return self::getArticleCreatedBy($articleId) === (int) $user->id;
    }

    /**
     * Loads the article author id from the content table.
     *
     * @param int $articleId Content item id
     *
     * @return int Author user id, or 0 when missing or on query failure
     */
    private static function getArticleCreatedBy(int $articleId): int
    {
        try {
            $db = CmsFactory::getContainer()->get(DatabaseInterface::class);

            return (int) $db->setQuery(
                $db->getQuery(true)
                    ->select($db->quoteName('created_by'))
                    ->from($db->quoteName('#__content'))
                    ->where($db->quoteName('id') . ' = :articleId')
                    ->bind(':articleId', $articleId, ParameterType::INTEGER)
            )->loadResult();
        } catch (Throwable) {
            return 0;
        }
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
