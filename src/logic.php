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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

/** @var Joomla\CMS\Document\HtmlDocument $this */

// template path
$tpath = $this->baseurl . 'templates/' . $this->template;

/** @var Joomla\CMS\Application\SiteApplication $app */
$app = Factory::getApplication();

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');

// site name
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

// menu
$menu = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';

// template params
$templateparams = $app->getTemplate(true)->params;

// check frontpage
($menu->home == 1) ? $home = true : $home = false;

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Logo file or site title param
if ($templateparams['logoFile']) {
    $logo = '<img src="' . Uri::root(true) . '/' . HTMLHelper::_('cleanImageURL', $templateparams['logoFile'])->url . '" alt="' . $sitename . '">';
} elseif ($templateparams['siteTitle']) {
    $logo = '<span class="font-bold text-2xl" title="' . $sitename . '">' . htmlspecialchars($templateparams['siteTitle'], ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = '<span class="font-bold text-2xl" title="' . $sitename . '">' . $sitename . '</span>';
}

// Logo file or site title param
if ($templateparams['logoFile']) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($templateparams['logoFile'], ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($templateparams['siteTitle']) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
}

// assets
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getWebAssetManager();
$wa->registerAndUseStyle('fontawesome', 'media/system/css/joomla-fontawesome.min.css');
$wa->useStyle('template.boilerplate.app');
$wa->useScript('template.boilerplate.app');
$wa->addInlineScript('document.getElementsByTagName("html")[0].className += " js";', [], []);