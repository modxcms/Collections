<?php

namespace Collections\Endpoint\Ajax;

class GetAuthors extends Endpoint
{
    protected $allowedMethod = ['GET', 'OPTIONS'];
    protected $sessionEnabled = [];

    function process()
    {
        $collection = isset($_GET['collection']) ? intval($_GET['collection']) : 0;

        if (empty($collection)) {
            return $this->failure('No collection was provided');
        }

        $templates = $this->fred->getFredTemplates();
        if (empty($templates)) {
            return $this->failure('No Fred templates');
        }


        $c = $this->modx->newQuery('modResource');
        $c->where([
            'parent' => $collection,
            'template:IN' => $templates
        ]);

        $c->select([
            'createdby' => 'distinct(createdby)'
        ]);

        $c->prepare();
        $c->stmt->execute();

        $authorIDs = $c->stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
        if (empty($authorIDs)) {
            return $this->data([]);
        }

        $cProfile = $this->modx->newQuery('modUserProfile');
        $cProfile->where([
            'internalKey:IN' => $authorIDs
        ]);
        $cProfile->select($this->modx->getSelectColumns('modUserProfile', 'modUserProfile', '', ['internalKey', 'fullname']));
        $cProfile->sortby('fullname', 'asc');
        $cProfile->prepare();
        $cProfile->stmt->execute();

        $authors = ['-1' => 'Any'];
        while($row = $cProfile->stmt->fetch(\PDO::FETCH_ASSOC)) {
            $authors[(string)$row['internalKey']] = $row['fullname'];
        }

        return $this->data($authors);
    }

}
