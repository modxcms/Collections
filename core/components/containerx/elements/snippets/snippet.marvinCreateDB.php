<?php
$containerx = $modx->getService('containerx','ContainerX',$modx->getOption('containerx.core_path',null,$modx->getOption('core_path').'components/containerx/').'model/containerx/',$scriptProperties);
if (!($containerx instanceof ContainerX)) return '';


$m = $modx->getManager();
$m->createObjectContainer('ContainerXCategoryExtendedFields');
$m->createObjectContainer('ContainerXLocation');
$m->createObjectContainer('ContainerXFeedback');
$m->createObjectContainer('ContainerXComment');
$m->createObjectContainer('ContainerXPhoto');
$m->createObjectContainer('ContainerXTag');
$m->createObjectContainer('ContainerXLocationType');
$m->createObjectContainer('ContainerXField');
$m->createObjectContainer('ContainerXFieldValue');
$m->createObjectContainer('ContainerXLocationTag');
$m->createObjectContainer('ContainerXLocationCategory');

return 'Table created.';
