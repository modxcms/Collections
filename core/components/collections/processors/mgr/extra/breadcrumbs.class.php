<?php

class CollectionsBreadcrumbsGetListProcessor extends modProcessor
{
    public function process()
    {
        $folder = (int)$this->getProperty('folder', 0);
        $collection = (int)$this->getProperty('collection', 0);

        if (($folder <= 0) || ($collection <= 0)) {
            return $this->failure();
        }

        /** @var modResource $resource */
        $resource = $this->modx->getObject('modResource', $folder);
        if (!$resource) {
            return $this->failure();
        }

        $haveParent = false;
        $parents = array(
            array(
                'id' => $resource->id,
                'text' => $resource->pagetitle
            )
        );

        while ($haveParent === false) {
            $parent = $resource->Parent;

            if ($parent->id == $collection) {
                $haveParent = true;
            } else {
                $parents[] = array(
                    'id' => $parent->id,
                    'text' => $parent->pagetitle
                );
                $resource = $parent;
            }

        }

        return $this->outputArray(array_reverse($parents));
    }
}

return 'CollectionsBreadcrumbsGetListProcessor';