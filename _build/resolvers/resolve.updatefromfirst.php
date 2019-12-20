<?php

use Collections\Model\CollectionTemplate;
use MODX\Revolution\modSystemSetting;
use MODX\Revolution\Transport\modTransportPackage;
use MODX\Revolution\modResource;

set_time_limit(0);
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            // http://forums.modx.com/thread/88734/package-version-check#dis-post-489104
            $c = $modx->newQuery(modTransportPackage::class);
            $c->where([
                'workspace' => 1,
                "(SELECT
                        `signature`
                      FROM {$modx->getTableName(modTransportPackage::class)} AS `latestPackage`
                      WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
                      ORDER BY
                         `latestPackage`.`version_major` DESC,
                         `latestPackage`.`version_minor` DESC,
                         `latestPackage`.`version_patch` DESC,
                         IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                         `latestPackage`.`release_index` DESC
                      LIMIT 1,1) = `modTransportPackage`.`signature`",
            ]);
            $c->where([
                'modTransportPackage.package_name' => 'collections',
                'installed:IS NOT' => null
            ]);

            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject(modTransportPackage::class, $c);

            $srcPath = $modx->getOption('collections.core_path', null, $modx->getOption('core_path') . 'components/collections/') . 'src/';
            $modx->addPackage('Collections\Model', $srcPath, null, 'Collections\\');

            if ($oldPackage && $oldPackage->compareVersion('2.0.0-pl', '>')) {
                $date = $modx->getObject(modSystemSetting::class, ['key' => 'collections.mgr_date_format']);
                if (!$date) {
                    $date = $modx->newObject(modSystemSetting::class);
                    $date->set('key', 'collections.mgr_date_format');
                    $date->set('namespace', 'collections');
                    $date->set('xtype', 'textfield');
                }

                $date->set('value', 'M d');
                $date->save();

                $time = $modx->getObject(modSystemSetting::class, ['key' => 'collections.mgr_time_format']);
                if (!$time) {
                    $time = $modx->newObject(modSystemSetting::class);
                    $time->set('key', 'collections.mgr_time_format');
                    $time->set('namespace', 'collections');
                    $time->set('xtype', 'textfield');
                }

                $time->set('value', 'g:i a');
                $time->save();
            }

            if ($oldPackage && $oldPackage->compareVersion('3.4.0-pl', '>')) {
                /** @var modResource[] $collections */
                $collections = $modx->getIterator(modResource::class, ['class_key' => 'CollectionContainer']);
                foreach ($collections as $collection) {
                    $modx->updateCollection(modResource::class, ['show_in_tree' => 0], ['parent' => $collection->id, 'class_key:!=' => 'CollectionContainer']);
                }

                /** @var CollectionTemplate[] $views */
                $views = $modx->getIterator(CollectionTemplate::class);
                foreach ($views as $view) {
                    $buttons = $view->get('buttons');
                    if (strpos($buttons, 'open') === false) {
                        $buttons = 'open,' . $buttons;
                        $view->set('buttons', $buttons);
                        $view->save();
                    }
                }
            }

            if ($oldPackage && $oldPackage->compareVersion('3.7.0-pl', '>')) {
                /** @var CollectionTemplate[] $views */
                $views = $modx->getIterator(CollectionTemplate::class);
                foreach ($views as $view) {
                    $buttons = $view->get('buttons');
                    if (strpos($buttons, 'changeparent') === false) {
                        $buttons = $buttons . ',changeparent';
                        $view->set('buttons', $buttons);
                        $view->save();
                    }
                }
            }

            break;
    }
}
return true;
