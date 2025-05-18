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

// variables
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\AuthenticationHelper;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$extraButtons = AuthenticationHelper::getLoginButtons('form-login');
$app = Factory::getApplication();

// Logo file or site title param
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getWebAssetManager();

$wa->useStyle('template.boilerplate.app');
$wa->useScript('template.boilerplate.app');
$wa->addInlineScript('document.getElementsByTagName("html")[0].className += " js";', [], []);

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Defer font awesome
$wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

    <head>
        <jdoc:include type="metas" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <jdoc:include type="styles" />
        <jdoc:include type="scripts" />
    </head>

    <body>
        <div class="mx-auto py-8 max-w-4xl text-center container">
            <div class="bg-white shadow-md p-6 rounded-lg">
                <div class="mb-6">
                    <?php if (!empty($logo)): ?>
                        <h1><?php echo $logo; ?></h1>
                    <?php else: ?>
                        <h1 class="mb-4 text-primary text-4xl"><?php echo $sitename; ?></h1>
                    <?php endif; ?>
                    <?php if ($app->get('offline_image')): ?>
                        <?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, [], false, 0); ?>
                    <?php endif; ?>
                    <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != ''): ?>
                        <p class="text-lg"><?php echo $app->get('offline_message'); ?></p>
                    <?php elseif ($app->get('display_offline_message', 1) == 2): ?>
                        <p class="text-lg"><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
                    <?php endif; ?>
                </div>
                <div class="mx-auto py-8 max-w-xs text-left">
                    <jdoc:include type="message" />
                    <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
                        <fieldset class="flex flex-col">
                            <label for="username" class="mb-2"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                            <input name="username" class="mb-4 form-input" id="username" type="text">
                            <label for="password" class="mb-2"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                            <input name="password" class="mb-4 form-input" id="password" type="password">

                            <?php foreach ($extraButtons as $button):
                                $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                                    return substr($key, 0, 5) == 'data-';
                                });
                                ?>
                                <div class="mb-4">
                                    <button type="button" class="btn btn-primary <?php echo $button['class'] ?? '' ?>" <?php foreach ($dataAttributeKeys as $key): ?>         <?php echo $key ?>="<?php echo $button[$key] ?>" <?php endforeach; ?>     <?php if ($button['onclick']): ?> onclick="<?php echo $button['onclick'] ?>" <?php endif; ?>
                                        title="<?php echo Text::_($button['label']) ?>" id="<?php echo $button['id'] ?>">
                                        <?php if (!empty($button['icon'])): ?>
                                            <span class="<?php echo $button['icon'] ?>"></span>
                                        <?php elseif (!empty($button['image'])): ?>
                                            <?php echo $button['image']; ?>
                                        <?php elseif (!empty($button['svg'])): ?>
                                            <?php echo $button['svg']; ?>
                                        <?php endif; ?>
                                        <?php echo Text::_($button['label']) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>

                            <button type="submit" name="Submit"
                                class="mt-4 btn btn-primary"><?php echo Text::_('JLOGIN'); ?></button>

                            <input type="hidden" name="option" value="com_users">
                            <input type="hidden" name="task" value="user.login">
                            <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
                            <?php echo HTMLHelper::_('form.token'); ?>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </body>

</html>