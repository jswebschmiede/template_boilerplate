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

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
$doc = Factory::getApplication()->getDocument();
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

$templateparams = $displayData['templateparams'];
$logo = $displayData['logo'];
$home = $displayData['home'];

?>

<header
    class="f-header bg-white absolute top-0 flex items-center left-0 js-f-header w-full h-(--f-header-height) z-[3] <?php echo ((bool) $templateparams['stickyHeader']) ? 'f-header--sticky' : null; ?>"
    data-element="header">
    <div class="mx-auto w-full-p-1 lg:w-full-p-2 max-w-big f-header__mobile-content">
        <?php if ($home): ?>
            <div class="f-header__logo">
                <?php if ((bool) $templateparams['brand']): ?>
                    <?php echo $logo; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <a href="<?php echo Uri::base(); ?>" class="f-header__logo">
                <?php if ((bool) $templateparams['brand']): ?>
                    <?php echo $logo; ?>
                <?php endif; ?>
            </a>
        <?php endif ?>

        <button class="anim-menu-btn js-anim-menu-btn f-header__nav-control js-tab-focus" aria-label="Toggle menu">
            <i class="anim-menu-btn__icon anim-menu-btn__icon--close" aria-hidden="true"></i>
        </button>
    </div>

    <div class="f-header__nav" role="navigation">
        <div class="lg:justify-between f-header__nav-grid mx-auto w-full-p-1 lg:w-full-p-2 max-w-big">
            <div class="f-header__nav-logo-wrapper grow">
                <?php if ($home): ?>
                    <div class="f-header__logo">
                        <?php if ((bool) $templateparams['brand']): ?>
                            <?php echo $logo; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <a href="<?php echo Uri::base(); ?>" class="f-header__logo">
                        <?php if ((bool) $templateparams['brand']): ?>
                            <?php echo $logo; ?>
                        <?php endif; ?>
                    </a>
                <?php endif ?>
            </div>

            <?php if ($doc->countModules('main-menu', true)): ?>
                <jdoc:include type="modules" name="main-menu" style="none" />
            <?php endif ?>

        </div>
    </div>
</header>