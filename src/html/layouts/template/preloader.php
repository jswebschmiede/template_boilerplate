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

use Joomla\CMS\Language\Text;

?>

<div class="visible z-50 fixed inset-0 flex justify-center items-center bg-white transition spinner-wrapper"
    id="loader">
    <div class="circle-loader circle-loader--v2" role="alert">
        <p class="circle-loader__label"><?php echo Text::_("TPL_TEMPLATE_BOILERPLATE_PRELOADER_TEXT"); ?></p>
        <div aria-hidden="true">
            <svg class="circle-loader__svg" width="48" height="48" viewBox="0 0 48 48">
                <circle class="circle-loader__base" cx="24" cy="24" r="19" fill="none" stroke="currentColor"
                    stroke-width="2" />
                <circle class="circle-loader__fill" cx="24" cy="24" r="19" fill="none" stroke="currentColor"
                    stroke-width="2" />
            </svg>
        </div>
    </div>
</div>
