<?php
/*
 * This file is part of the Fred package.
 *
 * Copyright (c) MODX, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Collections\Endpoint;

class Ajax
{
    /** @var \modX */
    protected $modx;

    /** @var \Collections */
    protected $collections;
    
    /** @var \Fred */
    protected $fred = null;

    /**
     * Endpoint constructor.
     * @param \Collections $collections
     */
    public function __construct(\Collections &$collections)
    {
        $this->collections =& $collections;
        $this->modx =& $collections->modx;

        $corePath = $this->modx->getOption('fred.core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/fred/');
        /** @var \Fred $fred */
        $fred = $this->modx->getService(
            'fred',
            'Fred',
            $corePath . 'model/fred/',
            array(
                'core_path' => $corePath
            )
        );

        if (($fred instanceof \Fred)) {
            $this->fred = $fred;
        }
    }
    
    public function run()
    {
        if ($this->fred === null) {
            http_response_code(500);
            return;
        }
        
        if (!$this->modx->user) {
            http_response_code(401);
            return;
        }

        try {
            $payload = $this->fred->getJWTPayload();

            $this->modx->switchContext($payload['context']);

            if (!$this->modx->hasPermission('fred')) {
                http_response_code(403);
                return;
            }

            if ($payload['iss'] !== $this->modx->user->id) {
                http_response_code(403);
                return;
            }
        } catch (\Exception $e) {
            http_response_code(403);
            return;
        }

        $action = $this->modx->getOption('action', $_REQUEST, '');
        if (empty($action)) return;

        $action = str_replace('/', '', $action);
        $action = str_replace('\\', '', $action);
        $action = explode('-', $action);
        $action = array_map('ucfirst', $action);
        $action = implode('', $action);

        $className = "\\Collections\\Endpoint\\Ajax\\{$action}";
        if (class_exists($className) !== true) {
            http_response_code(404);
            return;
        }

        /** @var Ajax\Endpoint $ajaxEndpoint */
        $ajaxEndpoint = new $className($this->collections, $this->fred, $payload);

        header('Content-Type: application/json; charset=UTF-8');

        echo $ajaxEndpoint->run();

        return;
    }
}