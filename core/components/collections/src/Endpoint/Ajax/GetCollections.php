<?php

namespace Collections\Endpoint\Ajax;

use Collections\Model\CollectionContainer;
use MODX\Revolution\modResource;

class GetCollections extends Endpoint
{
    protected $allowedMethod = ['GET', 'OPTIONS'];

    function process()
    {
        $context = $this->getClaim('context');

        $c = $this->modx->newQuery(modResource::class);
        $c->where([
            'class_key' => CollectionContainer::class,
            'context_key' => $context
        ]);
        $c->sortby('pagetitle');

        /** @var \modResource[] $resources */
        $resources = $this->modx->getIterator(modResource::class, $c);
        $data = [];

        foreach ($resources as $resource) {
            $data[] = [
                'id' => $resource->get('id'),
                'pagetitle' => $resource->get('pagetitle'),
                'published' => $resource->get('published'),
                'deleted' => $resource->get('deleted'),
            ];
        }

        return $this->data(['collections' => $data]);
    }
}
