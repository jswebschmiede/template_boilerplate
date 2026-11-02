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

use Joomla\CMS\Language\Text;
use JSch\Template\Boilerplate\Site\Helper\ThemeHelper;

defined('_JEXEC') or die;

if (!ThemeHelper::shouldShowAdminBar()) {
    return;
}

$links = ThemeHelper::getAdminBarLinks();

if ($links === []) {
    return;
}

$barLabel = Text::_('TPL_TEMPLATE_BOILERPLATE_ADMINBAR');

/**
 * Renders a 16px stroke icon for an admin bar item.
 *
 * @param string $icon Icon key
 *
 * @return string Inline SVG markup
 */
$renderIcon = static function (string $icon): string {
    $paths = [
        'backend' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1"/><rect x="13.5" y="3.5" width="7" height="7" rx="1"/><rect x="3.5" y="13.5" width="7" height="7" rx="1"/><rect x="13.5" y="13.5" width="7" height="7" rx="1"/>',
        'article' => '<path d="M15.232 5.232a2.5 2.5 0 0 1 3.536 3.536L8.5 19.036 4 20l.964-4.5 10.268-10.268Z"/>',
        'modules' => '<path d="M12 3 4.5 7.5v9L12 21l7.5-4.5v-9L12 3Z"/><path d="M4.5 7.5 12 12l7.5-4.5M12 12v9"/>',
        'menu'    => '<path d="M5 7h14M5 12h14M5 17h10"/>',
    ];

    $path = $paths[$icon] ?? $paths['backend'];

    return '<svg class="adminbar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . $path
        . '</svg>';
};

?>
<nav class="adminbar" aria-label="<?php echo htmlspecialchars($barLabel, ENT_QUOTES, 'UTF-8'); ?>">
    <ul class="adminbar__list">
        <?php foreach ($links as $link) : ?>
            <li class="adminbar__item">
                <a class="adminbar__link"
                    href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>"
                    target="_blank"
                    rel="noopener noreferrer">
                    <?php echo $renderIcon($link['icon']); ?>
                    <span class="adminbar__label"><?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
