<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$modId = 'mod-custom' . $module->id;

if ($params->get('backgroundimage')) {
    /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
    $wa = $app->getDocument()->getWebAssetManager();
    $wa->addInlineStyle('
#' . $modId . '{background-image: url("' . Uri::root(true) . '/' . HTMLHelper::_('cleanImageURL', $params->get('backgroundimage'))->url . '");}
', ['name' => $modId]);
}
?>

<div class="relative flex justify-center items-center bg-cover bg-no-repeat bg-center p-4 h-[430px] md:h-[500px] lg:h-[600px] mod-custom custom banner"
    id="<?php echo $modId; ?>">

    <?php if (!empty($module->content)): ?>
        <div class="z-[2] relative reveal-fx--translate-up reveal-fx">
            <?php echo $module->content; ?>
        </div>

        <div class="absolute inset-0 bg-black/25"></div>
    <?php endif ?>
</div>