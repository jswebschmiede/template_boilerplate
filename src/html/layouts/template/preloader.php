<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.joomla-codyhouse-starter
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>


<div class="spinner-wrapper fixed inset-0 bg-white z-50 visible transition flex justify-center items-center" id="loader">
	<div class="circle-loader circle-loader--v2" role="alert">
		<p class="circle-loader__label"><?php echo Text::_("TPL_JCS_PRELOADER_TEXT"); ?></p>
		<div aria-hidden="true">
			<svg class="circle-loader__svg" width="48" height="48" viewBox="0 0 48 48">
				<circle class="circle-loader__base" cx="24" cy="24" r="19" fill="none" stroke="currentColor" stroke-width="2" />
				<circle class="circle-loader__fill" cx="24" cy="24" r="19" fill="none" stroke="currentColor" stroke-width="2" />
			</svg>
		</div>
	</div>
</div>