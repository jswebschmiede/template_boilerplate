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

<div id="<?php echo $modId; ?>"
    class="mx-auto w-p-1 lg:w-p-2 max-w-wide reveal-fx--translate-up mod-custom wide reveal-fx">
    <?php echo $module->content; ?>
</div>