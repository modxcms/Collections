<?php
namespace Collections\Processors\Extra;

use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Processor;

class GetDerivates extends Processor
{
    public function checkPermissions()
    {
        return $this->modx->hasPermission('class_map');
    }

    public function initialize()
    {
        $this->setDefaultProperties([
            'class' => '',
            'skip' => 'modXMLRPCResource',
        ]);
        return true;
    }

    public function process()
    {
        $class = $this->getProperty('class');
        if (empty($class)) $this->failure($this->modx->lexicon('class_err_ns'));

        $skip = explode(',', $this->getProperty('skip'));
        $descendants = $this->modx->getDescendants($class);

        $list = [];
        foreach ($descendants as $descendant) {
            if (in_array($descendant, $skip)) continue;

            /** @var modResource $obj */
            $obj = $this->modx->newObject($descendant);
            if (!$obj) continue;

            if ($class == 'MODX\\Revolution\\modResource' && !$obj->allowListingInClassKeyDropdown) continue;
            if ($class == 'MODX\\Revolution\\modResource') {
                $name = $obj->getResourceTypeName();
            } else {
                $name = $descendant;
            }

            $list[$descendant] = [
                'id' => $descendant,
                'name' => $name,
            ];
        }

        return $this->outputArray($list);
    }
}
