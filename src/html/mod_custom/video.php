<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$modId = 'mod-custom' . $module->id;
?>

<div id="<?php echo $modId; ?>" class="mod-custom video max-w-content mx-auto reveal-fx reveal-fx--translate-up">
    <div class="aspect-w-16 aspect-h-9">
        <?php echo strip_tags($module->content, ['iframe']); ?>
    </div>
</div>