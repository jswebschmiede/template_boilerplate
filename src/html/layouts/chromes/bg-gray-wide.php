<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.hoennetreff
 *
 * @copyright   (C) 2024. All rights reserved by Jörg Schöneburg
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Layout\LayoutHelper;

$module = $displayData['module'];
$params = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '') {
    return;
}

$moduleTag = $params->get('module_tag', 'div');
$moduleAttribs = [];
$moduleAttribs['class'] = 'default bg-gray-100 entry-content wide mt-12 lg:mt-24 py-12 lg:py-24 ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');

?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?>>

    <div class="mx-auto w-full-p-1 lg:w-full-p-2 max-w-wide">
        <?php if ($module->showtitle): ?>
            <?php echo LayoutHelper::render('template.heading', ['params' => $params, 'module' => $module, 'attribs' => $attribs]); ?>
        <?php endif; ?>

        <?php echo $module->content; ?>
    </div>
</<?php echo $moduleTag; ?>>