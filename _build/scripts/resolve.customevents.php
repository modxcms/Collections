<?php
/**
 * @var \Teleport\Transport\Transport $transport
 * @var array $object
 * @var array $options
 */
 
if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $transport->xpdo;        

    $events = [
        'CollectionsOnResourceSort',
    ];

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            foreach ($events as $eventName) {
                $event = $modx->getObject(\MODX\Revolution\modEvent::class, ['name' => $eventName]);
                if (!$event) {
                    $event = $modx->newObject(\MODX\Revolution\modEvent::class);
                    $event->set('name', $eventName);
                    $event->set('service', 6);
                    $event->set('groupname', 'Collections');
                    $event->save();
                }
            }

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            foreach ($events as $eventName) {
                $event = $modx->getObject(\MODX\Revolution\modEvent::class, ['name' => $eventName]);
                if ($event) {
                    $event->remove();
                }
            }

            break;
    }
}
return true;
