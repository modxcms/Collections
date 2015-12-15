<?php
set_time_limit(0);
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;

            // http://forums.modx.com/thread/88734/package-version-check#dis-post-489104
            $c = $modx->newQuery('transport.modTransportPackage');
            $c->where(array(
                'workspace' => 1,
                "(SELECT
                        `signature`
                      FROM {$modx->getTableName('modTransportPackage')} AS `latestPackage`
                      WHERE `latestPackage`.`package_name` = `modTransportPackage`.`package_name`
                      ORDER BY
                         `latestPackage`.`version_major` DESC,
                         `latestPackage`.`version_minor` DESC,
                         `latestPackage`.`version_patch` DESC,
                         IF(`release` = '' OR `release` = 'ga' OR `release` = 'pl','z',`release`) DESC,
                         `latestPackage`.`release_index` DESC
                      LIMIT 1,1) = `modTransportPackage`.`signature`",
            ));
            $c->where(array(
                'modTransportPackage.package_name' => 'collections',
                'installed:IS NOT' => null
            ));

            /** @var modTransportPackage $oldPackage */
            $oldPackage = $modx->getObject('transport.modTransportPackage', $c);

            $modelPath = $modx->getOption('collections.core_path',null,$modx->getOption('core_path').'components/collections/').'model/';
            $modx->addPackage('collections',$modelPath);

            if ($oldPackage && $oldPackage->compareVersion('2.0.0-pl', '>')) {
                $date = $modx->getObject('modSystemSetting', array('key' => 'collections.mgr_date_format'));
                if (!$date) {
                    $date = $modx->newObject('modSystemSetting');
                    $date->set('key', 'collections.mgr_date_format');
                    $date->set('namespace', 'collections');
                    $date->set('xtype', 'textfield');
                }

                $date->set('value', 'M d');
                $date->save();

                $time = $modx->getObject('modSystemSetting', array('key' => 'collections.mgr_time_format'));
                if (!$time) {
                    $time = $modx->newObject('modSystemSetting');
                    $time->set('key', 'collections.mgr_time_format');
                    $time->set('namespace', 'collections');
                    $time->set('xtype', 'textfield');
                }

                $time->set('value', 'g:i a');
                $time->save();
            }

            if ($oldPackage && $oldPackage->compareVersion('2.1.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'child_template');
                $manager->addField('CollectionTemplate', 'child_resource_type');
                $manager->addField('CollectionTemplate', 'resource_type_selection');
            }

            if ($oldPackage && $oldPackage->compareVersion('2.2.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'tab_label');
                $manager->addField('CollectionTemplate', 'button_label');
                $manager->addField('CollectionTemplate', 'content_place');
            }

            if ($oldPackage && $oldPackage->compareVersion('3.0.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'view_for');
                $manager->addField('CollectionTemplate', 'link_label');
                $manager->addField('CollectionTemplate', 'context_menu');
                $manager->addField('CollectionTemplate', 'buttons');
                $manager->addField('CollectionTemplate', 'allowed_resource_types');

                $manager->addField('CollectionTemplateColumn', 'php_renderer');
            }

            if ($oldPackage && $oldPackage->compareVersion('3.1.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'back_to_collection_label');
                $manager->addField('CollectionTemplate', 'back_to_selection_label');
                $manager->addField('CollectionTemplate', 'selection_create_sort');
                $manager->addField('CollectionTemplate', 'child_hide_from_menu');
                $manager->addField('CollectionTemplate', 'child_published');
                $manager->addField('CollectionTemplate', 'child_cacheable');
                $manager->addField('CollectionTemplate', 'child_searchable');
                $manager->addField('CollectionTemplate', 'child_richtext');
                $manager->addField('CollectionTemplate', 'child_content_type');
            }
            
            if ($oldPackage && $oldPackage->compareVersion('3.2.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'permanent_sort_before');
                $manager->addField('CollectionTemplate', 'permanent_sort_after');
                $manager->addField('CollectionTemplate', 'sort_type');

                $manager->addField('CollectionTemplateColumn', 'sort_type');
            }
            
            if ($oldPackage && $oldPackage->compareVersion('3.2.1-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'parent');
                $manager->addField('CollectionTemplate', 'child_content_disposition');
            }
            
            if ($oldPackage && $oldPackage->compareVersion('3.4.0-pl', '>')) {
                $manager = $modx->getManager();
                $manager->addField('CollectionTemplate', 'selection_link_condition');
                $manager->alterField('CollectionTemplate', 'selection_link_condition');

                /** @var modResource[] $collections */
                $collections = $modx->getIterator('modResource', array('class_key' => 'CollectionContainer'));
                foreach ($collections as $collection) {
                    $modx->updateCollection('modResource', array('show_in_tree' => 0), array('parent' => $collection->id, 'class_key:!=' => 'CollectionContainer'));
                }
                
                /** @var CollectionTemplate[] $views */
                $views = $modx->getIterator('CollectionTemplate');
                foreach ($views as $view) {
                    $buttons = $view->get('buttons');
                    if (strpos($buttons, 'open') === false) {
                        $buttons = 'open,' . $buttons;
                        $view->set('buttons', $buttons);
                        $view->save();
                    }
                }
            }

            break;
    }
}
return true;