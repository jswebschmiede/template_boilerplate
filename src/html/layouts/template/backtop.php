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

<div class='invisible right-5 bottom-5 z-40 fixed flex justify-center items-center bg-white opacity-0 rounded-[9999px] w-[3rem] h-[3rem] transition-all translate-y-[5px] cursor-pointer progress-wrap'
    aria-label='<?php echo Text::_("TPL_TEMPLATE_BOILERPLATE_BACKTOTOP_LABEL"); ?>'
    title='<?php echo Text::_("TPL_TEMPLATE_BOILERPLATE_BACKTOTOP"); ?>'>
    <svg class='svg-content' width='100%' height='100%' viewBox='-1 -1 102 102'>
        <path class="box-border stroke-[5px] stroke-primary transition-all" fill='none'
            d='M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98'
            style='transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 0.351157;'>
        </path>
    </svg>

    <div
        class='top-1/2 left-1/2 absolute justify-center items-center text-primary text-center transition-all -translate-x-1/2 -translate-y-1/2 progress-wrap__icon'>
        <svg width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'
            stroke-linecap='round' stroke-linejoin='round' class='w-6 h-6 feather feather-arrow-up'>
            <line x1='12' y1='19' x2='12' y2='5'></line>
            <polyline points='5 12 12 5 19 12'></polyline>
        </svg>
    </div>
</div>
