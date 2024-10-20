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

/** @var Joomla\CMS\Document\HtmlDocument $doc */

$templateparams = $displayData['templateparams'];
$logo = $displayData['logo'];

$doc = Factory::getApplication()->getDocument();
?>

<header
    class="f-header bg-white absolute top-0 left-0 js-f-header w-full h-[--f-header-height] z-[3] <?php echo ((bool) $templateparams['stickyHeader']) ? 'f-header--sticky' : null; ?>"
    data-element="header">
    <div class="f-header__mobile-content w-full-p-1 lg:w-full-p-2 mx-auto max-w-big">
        <a href="<?php echo Uri::base(); ?>" class="f-header__logo">
            <?php if ((bool) $templateparams['brand']): ?>
                <?php echo $logo; ?>
            <?php endif; ?>
        </a>

        <button class="anim-menu-btn js-anim-menu-btn f-header__nav-control js-tab-focus" aria-label="Toggle menu">
            <i class="anim-menu-btn__icon anim-menu-btn__icon--close" aria-hidden="true"></i>
        </button>
    </div>

    <div class="f-header__nav" role="navigation">
        <div class="f-header__nav-grid lg:justify-between w-full-p-1 lg:w-full-p-2 mx-auto max-w-big">
            <div class="f-header__nav-logo-wrapper grow">
                <a href="<?php echo Uri::base(); ?>" class="f-header__logo">
                    <?php if ((bool) $templateparams['brand']): ?>
                        <?php echo $logo; ?>
                    <?php endif; ?>
                </a>
            </div>


            <?php if ($doc->countModules('main-menu', true)): ?>
                <jdoc:include type="modules" name="main-menu" style="none" />
            <?php endif ?>

        </div>
    </div>
</header>