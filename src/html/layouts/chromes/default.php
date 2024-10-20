<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.template_boilerplate
 *
 * @copyright   (C) 2024. All rights reserved by Jörg Schöneburg
 * @license     MIT License (MIT) see LICENSE.txt
 * @author      Jörg Schöneburg <info@joerg-schoeneburg.de> - https://joerg-schoeneburg.de
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

$module = $displayData['module'];
$params = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '') {
    return;
}

$moduleTag = $params->get('module_tag', 'div');
$moduleAttribs = [];
$moduleAttribs['class'] = 'default pt-12 lg:pt-24 max-w-wide mx-auto w-full-p-1 lg:w-full-p-2 ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');
$moduleId = htmlspecialchars($params->get('module_id', ''), ENT_QUOTES, 'UTF-8');
$headerTag = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
$headerClass = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');
$headerAttribs = [];

$headerAttribs['class'] = 'mb-8';

// Only output a header class if one is set
if ($headerClass !== '') {
    $headerAttribs['class'] .= $headerClass;
}
if ($moduleId !== '') {
    $moduleAttribs['id'] = $moduleId;
}

// Only add aria if the moduleTag is not a div
if ($moduleTag !== 'div') {
    if ($module->showtitle):
        $moduleAttribs['aria-labelledby'] = 'mod-' . $module->id;
        $headerAttribs['id'] = 'mod-' . $module->id;
    else:
        $moduleAttribs['aria-label'] = $module->title;
    endif;
}

$header = '<' . $headerTag . ' ' . ArrayHelper::toString($headerAttribs) . '>' . $module->title . '</' . $headerTag . '>';
?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?>>

    <?php if ($module->showtitle): ?>
        <?php echo $header; ?>
    <?php endif; ?>
    <?php echo $module->content; ?>

</<?php echo $moduleTag; ?>>