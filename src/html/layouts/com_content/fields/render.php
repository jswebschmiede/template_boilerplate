<?php

/**
 * Override of the layout "fields.render" for com_content (test for JS Blocks).
 *
 * Same as components/com_fields/layouts/fields/render.php, except that fields marked by
 * plg_fields_jsblock ($field->jsblocks) are output directly, without <li> and label.
 * Other fields are still rendered as list; consecutive fields share one <ul>, order is kept.
 */

defined('_JEXEC') or die;

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

if (!array_key_exists('item', $displayData) || !array_key_exists('context', $displayData)) {
    return;
}

$item = $displayData['item'];

if (!$item) {
    return;
}

$context = $displayData['context'];

if (!$context) {
    return;
}

$fields = $displayData['fields'] ?? ($item->jcfields ?: FieldsHelper::getFields($context, $item, true));

if (empty($fields)) {
    return;
}

$output = [];
$list   = [];

foreach ($fields as $field) {
    if (!isset($field->value) || trim($field->value) === '') {
        continue;
    }

    // Block fields: the value is the rendered layout "blocks" of com_jsblocks
    if (!empty($field->jsblocks)) {
        if ($list) {
            $output[] = '<ul class="fields-container">' . implode("\n", $list) . '</ul>';
            $list     = [];
        }

        $output[] = $field->value;

        continue;
    }

    $class   = $field->name . ' ' . $field->params->get('render_class');
    $layout  = $field->params->get('layout', 'render');
    $content = FieldsHelper::render($context, 'field.' . $layout, ['field' => $field]);

    if (trim($content) === '') {
        continue;
    }

    $list[] = '<li class="field-entry ' . $class . '">' . $content . '</li>';
}

if ($list) {
    $output[] = '<ul class="fields-container">' . implode("\n", $list) . '</ul>';
}

echo implode("\n", $output);
