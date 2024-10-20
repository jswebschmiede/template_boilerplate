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

<div id="<?php echo $modId; ?>" class="mod-custom custom">
    <div class="prose lg:prose-lg">
        <?php echo $module->content; ?>
    </div>
</div>