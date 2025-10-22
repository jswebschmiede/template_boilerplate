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

<div id="scroll-top"
    class="invisible right-5 bottom-5 z-50 fixed flex justify-center items-center opacity-0 w-12 h-12 transition-all translate-y-[5px] scroll-top cursor-pointer"
    href="#top" title="<?php echo Text::_("TPL_TEMPLATE_BOILERPLATE_BACKTOTOP"); ?>"
    aria-label="<?php echo Text::_("TPL_TEMPLATE_BOILERPLATE_BACKTOTOP_LABEL"); ?>" role="button">

    <div
        class="top-1/2 left-1/2 z-[1] absolute flex justify-center items-center bg-primary rounded-full w-9 h-9 -translate-x-1/2 -translate-y-1/2">
        <svg class='stroke-white w-4 h-4' width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='currentColor'
            stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
            <line x1='12' y1='19' x2='12' y2='5'></line>
            <polyline points='5 12 12 5 19 12'></polyline>
        </svg>
    </div>

    <svg class="rounded-full w-11 h-11" version="1.1" xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 70 70" enable-background="new 0 0 70 70"
        xml:space="preserve">
        <circle id="progress-indicator" class="stroke-primary" fill="transparent" stroke="currentColor"
            style="stroke-width: 4px; stroke-linecap: round; stroke-dasharray: 1, 400; transition: stroke-dasharray 10ms linear;"
            stroke-miterlimit="10" cx="35" cy="35" r="34">
        </circle>
    </svg>
</div>