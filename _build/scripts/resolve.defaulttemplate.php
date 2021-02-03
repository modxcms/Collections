<?php
/**
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:

    case xPDOTransport::ACTION_UPGRADE:        
        /** @var modX $modx */
        $modx =& $transport->xpdo;        

        $templates = $modx->getCount(\Collections\Model\CollectionTemplate::class);
        if ($templates == 0) {
            /** @var Collections\Model\CollectionTemplate $template */
            $template = $modx->newObject(\Collections\Model\CollectionTemplate::class);
            $template->set('name', 'Blog');
            $template->set('description', 'A default view that works well for blogs.');
            $template->set('global_template', true);
            $template->set('bulk_actions', true);
            $template->set('allow_dd', true);
            $template->set('page_size', 10);
            $template->set('sort_field', 'publishedon');
            $template->set('sort_dir', 'desc');
            $template->set('child_template', null);
            $template->set('child_resource_type', 'modDocument');
            $template->set('resource_type_selection', true);

            $columns = [];
            $columns[0] = $modx->newObject(\Collections\Model\CollectionTemplateColumn::class);
            $columns[0]->fromArray([
                'label' => 'id',
                'name' => 'id',
                'hidden' => true,
                'sortable' => true,
                'width' => 40,
                'editor' => '',
                'renderer' => '',
                'position' => 0,
           ]);

            $columns[1] = $modx->newObject(\Collections\Model\CollectionTemplateColumn::class);
            $columns[1]->fromArray([
                'label' => 'publishedon',
                'name' => 'publishedon',
                'hidden' => false,
                'sortable' => true,
                'width' => 40,
                'editor' => '',
                'renderer' => 'Collections.renderer.datetimeTwoLines',
                'position' => 1,
            ]);

            $columns[2] = $modx->newObject(\Collections\Model\CollectionTemplateColumn::class);
            $columns[2]->fromArray([
                'label' => 'pagetitle',
                'name' => 'pagetitle',
                'hidden' => false,
                'sortable' => true,
                'width' => 170,
                'editor' => '',
                'renderer' => 'Collections.renderer.pagetitleWithButtons',
                'position' => 2,
            ]);

            $columns[3] = $modx->newObject(\Collections\Model\CollectionTemplateColumn::class);
            $columns[3]->fromArray([
                'label' => 'alias',
                'name' => 'alias',
                'hidden' => false,
                'sortable' => true,
                'width' => 100,
                'editor' => '',
                'renderer' => '',
                'position' => 3,
            ]);

            $columns[4] = $modx->newObject(\Collections\Model\CollectionTemplateColumn::class);
            $columns[4]->fromArray([
                'label' => 'resource_menuindex',
                'name' => 'menuindex',
                'hidden' => false,
                'sortable' => true,
                'width' => 50,
                'editor' => '{"xtype":"numberfield","allowNegative":false,"allowDecimal":false}',
                'renderer' => '',
                'position' => 4,
            ]);

            $template->addMany($columns, 'Columns');

            $template->save();
        }

        break;
}

return true;
