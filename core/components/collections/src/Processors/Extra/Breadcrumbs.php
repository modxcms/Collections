<?php
namespace Collections\Processors\Extra;

use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Processor;

class Breadcrumbs extends Processor
{
    public function process()
    {
        $folder = (int)$this->getProperty('folder', 0);
        $collection = (int)$this->getProperty('collection', 0);

        if (($folder <= 0) || ($collection <= 0)) {
            return $this->failure();
        }

        /** @var modResource $resource */
        $resource = $this->modx->getObject(modResource::class, $folder);
        if (!$resource) {
            return $this->failure();
        }

        $haveParent = false;
        $parents = [
            [
                'id' => $resource->id,
                'text' => $resource->pagetitle
            ]
        ];

        while ($haveParent === false) {
            $parent = $resource->Parent;

            if ($parent->id == $collection) {
                $haveParent = true;
            } else {
                $parents[] = [
                    'id' => $parent->id,
                    'text' => $parent->pagetitle
                ];
                $resource = $parent;
            }

        }

        return $this->outputArray(array_reverse($parents));
    }
}
