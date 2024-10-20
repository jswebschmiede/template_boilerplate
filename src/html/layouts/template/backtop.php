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
?>

<div class='progress-wrap fixed bottom-5 right-5 2xl:right-12 bg-white h-[3rem] w-[3rem] opacity-0 translate-y-[5px] invisible
        rounded-[9999px] flex items-center justify-center transition-all z-40 cursor-pointer'>
    <svg class='svg-content' width='100%' height='100%' viewBox='-1 -1 102 102'>
        <path class="stroke-primary-500 box-border transition-all stroke-[5px]" fill='none'
            d='M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98'
            style='transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 0.351157;'>
        </path>
    </svg>

    <div
        class='progress-wrap__icon items-center justify-center absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-center text-primary-500 transition-all'>
        <svg width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'
            stroke-linecap='round' stroke-linejoin='round' class='h-6 w-6 feather feather-arrow-up'>
            <line x1='12' y1='19' x2='12' y2='5'></line>
            <polyline points='5 12 12 5 19 12'></polyline>
        </svg>
    </div>
</div>