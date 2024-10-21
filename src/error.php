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
use Joomla\CMS\Layout\LayoutHelper;

/** @var Joomla\CMS\Document\ErrorDocument $this */

require(JPATH_THEMES . '/' . $this->template . '/logic.php');

// Get the error code
$errorCode = $this->error->getCode();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

    <head>
        <jdoc:include type="metas" />
        <jdoc:include type="styles" />
        <jdoc:include type="scripts" />
    </head>

    <body class="site error_site <?php echo $option
        . ($home ? ' home' . $task : ' site')
        . ' view-' . $view
        . ($layout ? ' layout-' . $layout : ' no-layout')
        . ($task ? ' task-' . $task : ' no-task')
        . ($itemid ? ' itemid-' . $itemid : '')
        . ($pageclass ? ' ' . $pageclass : '')
        . ($this->direction == 'rtl' ? ' rtl' : '');
    ?>">

        <?php if ($templateparams["pagePreloader"] == 1): ?>
            <?php echo LayoutHelper::render('tempalte.preloader'); ?>
        <?php endif; ?>

        <?php echo LayoutHelper::render('template.header', [
            'templateparams' => $templateparams,
            'logo' => $logo
        ]); ?>

        <?php if ($this->countModules('header', true)): ?>
            <jdoc:include type="modules" name="header" style="none" />
        <?php endif ?>

        <main data-element="content">
            <?php if ($this->countModules('error-' . $errorCode)): ?>
                <div class="container">
                    <jdoc:include type="message" />
                    <main>
                        <jdoc:include type="modules" name="error-<?php echo $errorCode; ?>" style="none" />
                    </main>
                </div>
            <?php else: ?>
                <div class="w-full-p-1 lg:w-full-p-2 mx-auto max-w-wide pt-12 lg:pt-24">
                    <div class="prose lg:prose-lg">
                        <h1 class="page-header"><?php echo Text::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
                        <div class="card">
                            <div class="card-body">
                                <jdoc:include type="message" />
                                <main>
                                    <p><strong><?php echo Text::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong>
                                    </p>
                                    <p><?php echo Text::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
                                    <ul>
                                        <li><?php echo Text::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                                        <li><?php echo Text::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
                                        <li><?php echo Text::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                                        <li><?php echo Text::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                                    </ul>
                                    <p><?php echo Text::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
                                    <p><a href="<?php echo $this->baseurl; ?>/index.php" class="btn btn-secondary"><span
                                                class="icon-home" aria-hidden="true"></span>
                                            <?php echo Text::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
                                    <hr>
                                    <p><?php echo Text::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
                                    <blockquote>
                                        <span class="badge bg-secondary"><?php echo $this->error->getCode(); ?></span>
                                        <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                                    </blockquote>
                                </main>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($this->debug): ?>
                        <div>
                            <?php echo $this->renderBacktrace(); ?>
                            <?php // Check if there are more Exceptions and render their data as well ?>
                            <?php if ($this->error->getPrevious()): ?>
                                <?php $loop = true; ?>
                                <?php // Reference $this->error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly ?>
                                <?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions ?>
                                <?php $this->setError($this->error->getPrevious()); ?>
                                <?php while ($loop === true): ?>
                                    <p><strong><?php echo Text::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
                                    <p><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <?php echo $this->renderBacktrace(); ?>
                                    <?php $loop = $this->setError($this->error->getPrevious()); ?>
                                <?php endwhile; ?>
                                <?php // Reset the main error object to the base error ?>
                                <?php $this->setError($this->error); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
        </main>

        <?php echo LayoutHelper::render('template.footer', [
            'templateparams' => $templateparams
        ]); ?>

        <?php if ($templateparams["backTop"] == 1): ?>
            <?php echo LayoutHelper::render('template.backtop'); ?>
        <?php endif; ?>

        <jdoc:include type="modules" name="debug" style="none" />
    </body>

</html>