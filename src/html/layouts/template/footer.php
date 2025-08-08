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

/** @var Joomla\CMS\Document\HtmlDocument $doc */

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
$doc = Factory::getApplication()->getDocument();
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

$templateparams = $displayData['templateparams'];

?>

<footer data-element="footer">
    <div class="container">
        <?php if ($doc->countModules('footer')): ?>
            <nav role="navigation">
                <jdoc:include type="modules" name="footer" style="none" />
            </nav>
        <?php endif ?>
    </div>
</footer>