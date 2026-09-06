<?php
use MODX\Revolution\modTemplateVar;
use MODX\Revolution\Sources\modMediaSource;

$imgName = $modx->getOption('value', $scriptProperties, '');

$tvName = $scriptProperties['column'];
$tvName = substr($tvName, 3);

$tv = $modx->getObject(modTemplateVar::class, ['name' => $tvName]);
$tvMediaSourceId = $tv->get('source');

$mediaSource = $modx->getObject(modMediaSource::class, $tvMediaSourceId);
$mediaSourceProperties = $mediaSource->getProperties();
$mediaPath = $mediaSourceProperties['basePath']['value'];

if ($imgName) {
	$thumb = $modx->runSnippet('pthumb', [
        'input' => MODX_BASE_URL . $mediaPath . $imgName,
        'options' => '&h=90&q=75'
	]);
	return '<img src="' . $thumb . '" height="90">';
}
else {
	return '';
}
