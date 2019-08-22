<?php

namespace Collections\Endpoint\Ajax;

class GetCollection extends Endpoint
{
    protected $allowedMethod = ['GET', 'OPTIONS'];
    protected $sessionEnabled = [];
    protected $availableFilters = ['query', 'published', 'createdby'];
    function process()
    {
        $collection = isset($_GET['collection']) ? intval($_GET['collection']) : 0;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $size = isset($_GET['size']) ? intval($_GET['size']) : 10;
        $sorters = isset($_GET['sorters']) ? $_GET['sorters'] : [];
        $filters = isset($_GET['filters']) ? $_GET['filters'] : [];

        $this->availableFilters = array_flip($this->availableFilters);

        if (!is_array($sorters)) $sorters = [];
        if (!is_array($filters)) $filters = [];

        if ($size > 50) $size = 50;
        if ($page < 1) $page = 1;

        if (empty($collection)) {
            return $this->failure('No collection was provided');
        }

        $templates = $this->fred->getFredTemplates();
        if (empty($templates)) {
            return $this->failure('No Fred templates');
        }

        /** @var \modResource $parentObject */
        $parentObject = $this->modx->getObject('modResource', $collection);

        /** @var \CollectionTemplate $template */
        $template = $this->collections->getCollectionsView($parentObject);

        $c = $this->modx->newQuery('modResource');
        $c->where([
            'parent' => $collection,
            'template:IN' => $templates
        ]);

        foreach ($filters as $filter) {
            if (!isset($this->availableFilters[$filter['field']])) continue;

            if ($filter['field'] === 'query') {
                if (!empty($filter['value'])) {
                    $c->where([
                        'pagetitle:LIKE' => '%' . $filter['value'] . '%'
                    ]);
                }
            } else {
                if ($filter['value'] != -1) {
                    $c->where([
                        $filter['field'] => $filter['value']
                    ]);
                }
            }
        }


        $total = $this->modx->getCount('modResource', $c);

        $c->limit($size, ($page - 1) * $size);

        $c->leftJoin('modUser', 'CreatedBy');
        $c->leftJoin('modUserProfile', 'Profile', 'CreatedBy.id = Profile.internalKey');

        $c->select($this->modx->getSelectColumns('modResource', 'modResource'));
        $c->select([
            'publishedon_combined' => 'IF(modResource.pub_date=0,modResource.publishedon, modResource.pub_date)'
        ]);
        $c->select($this->modx->getSelectColumns('modUserProfile', 'Profile', '', ['fullname']));

        $gridSort = [];
        $defaultDir = 'desc';

        if (empty($sorters)) {
            $gridSort[] = 'publishedon_combined';
        } else {
            foreach ($sorters as $sorter) {
                $gridSort[] = $sorter['field'];
                $defaultDir = $sorter['dir'];
            }
        }

        $gridSort = array_map('strtolower', $gridSort);

        $c = $this->permanentSort($c, $gridSort, $template->permanent_sort_before, $defaultDir);

        if (empty($sorters)) {
            $c->sortby('publishedon_combined', 'desc');
        } else {
            foreach ($sorters as $sorter) {
                $c->sortby($sorter['field'], $sorter['dir']);
            }
        }

        $c = $this->permanentSort($c, $gridSort, $template->permanent_sort_after, $defaultDir);

        /** @var \modResource[] $resources */
        $resources = $this->modx->getIterator('modResource', $c);
        $data = [];

        foreach ($resources as $resource) {

            $unpublishOn = (!empty($resource->get('unpub_date'))) ? $resource->get('unpub_date', 'Y-m-d') : '';
            $publishedonCombined = $resource->get('publishedon_combined');
            $publishedonCombined = !empty($publishedonCombined) ? date('Y-m-d', $publishedonCombined) : '';

            $data[] = [
                'id' => $resource->get('id'),
                'pagetitle' => $resource->get('pagetitle'),
                'menuindex' => $resource->get('menuindex'),
                'publishedon_combined' => $publishedonCombined,
                'unpub_date' => $unpublishOn,
                'published' => $resource->get('published'),
                'deleted' => $resource->get('deleted'),
                'fullUrl' => $this->getPreviewUrl($resource),
                'url' => $this->getPreviewUrl($resource, 'abs'),
                'fullname' => $resource->get('fullname')
            ];
        }

        return $this->data($data, ['last_page' => ceil($total / $size)]);
    }

    /**
     * @param \modResource $resource
     * @param string $schema
     * @return string
     */
    public function getPreviewUrl($resource, $schema = 'full') {
        $previewUrl = '';

        if (!$resource->get('deleted')) {
            $this->modx->setOption('cache_alias_map', false);
            $sessionEnabled = '';

            if (isset($this->sessionEnabled[$resource->get('context_key')])) {
                $sessionEnabled = $this->sessionEnabled[$resource->get('context_key')];
            } else {
                $ctxSetting = $this->modx->getObject('modContextSetting', array('context_key' => $resource->get('context_key'), 'key' => 'session_enabled'));
                if ($ctxSetting) {
                    $sessionEnabled = $ctxSetting->get('value') == 0 ? array('preview' => 'true') : '';
                }
                $this->sessionEnabled[$resource->get('context_key')] = $sessionEnabled;
            }
            $previewUrl = $this->modx->makeUrl($resource->get('id'), $resource->get('context_key'), $sessionEnabled, $schema, array('xhtml_urls' => false));
        }
        return $previewUrl;
    }

    protected function permanentSort(\xPDOQuery $c, $gridSort, $sortOptions, $defaultDir)
    {
        $sorts = explode(',', $sortOptions);
        foreach ($sorts as $sort) {
            $sort = explode('=', $sort);
            if (isset($sort[1])) {
                if (in_array($sort[0], ['publishedon', 'published', 'pub_date'])) {
                    $sort[0] = 'publishedon_combined';
                }
                if (($sort[0] != '*') && (!in_array(strtolower($sort[0]), $gridSort))) continue;
            }

            $options = (isset($sort[1])) ? $sort[1] : $sort[0];
            $options = explode(':', $options);
            if (empty($options[0])) continue;

            $options['field'] = $options[0];
            $options['dir'] = empty($options[1]) ? $defaultDir : $options[1];
            $options['type'] = empty($options[2]) ? null : $options[2];

            if (empty($options['type'])) {
                $c->sortby('`' . $options['field'] . '`', $options['dir']);
            } else {
                $c->sortby('CAST(`' . $options['field'] . '` as ' . $options['type'] . ')', $options['dir']);
            }
        }

        return $c;
    }
}
