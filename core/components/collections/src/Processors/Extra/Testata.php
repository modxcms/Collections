<?php
namespace Collections\Processors\Extra;

use MODX\Revolution\modResource;
use MODX\Revolution\Processors\Processor;

class Testata extends Processor
{
    public function process()
    {
        $collection = (int)$this->getProperty('collection', 0);
        $selection = (int)$this->getProperty('selection', 0);
		
		if ($collection <= 0) {
            return $this->failure();
        }
		$resource = $this->modx->getObject(modResource::class, $collection);
        if (!$resource) {
            return $this->failure();
        }
				
		//Discrimino a seconda che sia selezione oppure no per icona
		$resource->published?$classPub ='pubblicata':$classPub ='ritirata';
		if($selection)
		{
			$icona ='<i class="icon selectioncontainer"></i>';
		}else{
			$icona ='<i class="icon collectioncontainer"></i>';
		}
				
		//Contesto 
        $contextKey = $resource->context_key;
		$context = $this->modx->getContext( $contextKey );
		$contextName = $context->name;
		
		//Prima creo menu		
		//Prima parte del menu il contesto con la sua icona
		$menu = '<span class="eleMenu contesto"><i class="icon tree-context"></i>'.$contextName.'</span>';
		
		$pids = $this->modx->getParentIds($collection, 10, array('context' => $contextKey));
		$gids = array_reverse($pids);
		foreach($gids as $gid) 
		{
			if($gid == 0){continue;}
				
			$genit = $this->modx->getObject(modResource::class, $gid);
			if (!$genit){continue;}
			$genit->published?$classPubGen ='pubblicata':$classPubGen ='ritirata';
			$genit->hidemenu?$classMenuGen ='nomenu':$classMenuGen ='simenu';
			$menu .= '<span class="eleMenu '.$classPubGen.' '.$classMenuGen.'">'.$genit->pagetitle.'</span>';
		}
		//Ultimo elemento del menu la sua icona
		$menu .= '<span class="ultimo '.$classPub.'">'.$icona.'</span>';
		
		$RisAjax [] = $menu;
		
		//Adesso nome a cui aggiungo icona
		$nome = $icona;
		$nome .= $resource->pagetitle;
		$RisAjax [] = $nome;
		
		return $this->outputArray($RisAjax);
    }
}
