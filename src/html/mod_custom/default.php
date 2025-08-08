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

<div id="<?php echo $modId; ?>" class="reveal-fx--translate-up mod-custom custom reveal-fx">
    <?php echo $module->content; ?>
</div>