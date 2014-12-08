<?php
/**
 * The base class for Collections.
 *
 * @package collections
 */
class Collections {
    /** @var \modX $modx */
    public $modx;
    public $namespace = 'collections';
    /** @var array $config */
    public $config = array();
    /** @var array $chunks */
    public $chunks = array();

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $config, 'collections');

        $corePath = $this->getOption('core_path', $config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/collections/');
        $assetsUrl = $this->getOption('assets_url', $config, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/collections/');
        $connectorUrl = $assetsUrl.'connector.php';

        $taggerCorePath = $modx->getOption('tagger.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tagger/');

        if (file_exists($taggerCorePath . 'model/tagger/tagger.class.php')) {
            /** @var Tagger $tagger */
            $tagger = $modx->getService(
                'tagger',
                'Tagger',
                $taggerCorePath . 'model/tagger/',
                array(
                    'core_path' => $taggerCorePath
                )
            );
        } else {
            $tagger = null;
        }

        $quipCorePath = $modx->getOption('quip.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/quip/');

        if (file_exists($quipCorePath . 'model/quip/quip.class.php')) {
            /** @var Quip $quip */
            $quip = $modx->getService(
                'quip',
                'Quip',
                $quipCorePath . 'model/quip/',
                array(
                    'core_path' => $quipCorePath
                )
            );
        } else {
            $quip = null;
        }

        $this->config = array_merge(array(
            'assets_url' => $assetsUrl,
            'core_path' => $corePath,

            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'images/',

            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
            'templatesPath' => $corePath.'templates/',

            'taggerInstalled' => $tagger instanceof Tagger,
            'quipInstalled' => $quip instanceof Quip,
        ),$config);

        $this->modx->addPackage('collections',$this->config['modelPath']);
        $this->modx->lexicon->load('collections:default');
        $this->modx->lexicon->load('collections:selections');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    public function explodeAndClean($array, $delimiter = ',', $keepDuplicates = 0) {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values

        if ($keepDuplicates == 0) {
            $array = array_keys(array_flip($array));  // Remove duplicate fields
        }

        $array = array_filter($array);            // Remove empty values from array

        return $array;
    }

    /**
     * @param modResource $collection
     * @return CollectionTemplate
     */
    public function getCollectionsView($collection) {
        $template = null;

        /** @var CollectionSetting $collectionSetting */
        $collectionSetting = $this->modx->getObject('CollectionSetting', array('collection' => $collection->id));
        if ($collectionSetting) {
            if (intval($collectionSetting->template) > 0) {
                $template = $collectionSetting->Template;
            }
        }

        if ($template == null) {
            /** @var CollectionResourceTemplate $resourceTemplate */
            $resourceTemplate = $this->modx->getObject('CollectionResourceTemplate', array('resource_template' => $collection->template));
            if ($resourceTemplate) {
                $template = $resourceTemplate->CollectionTemplate;
            } else {
                $template = $this->modx->getObject('CollectionTemplate', array('global_template' => 1));
            }
        }

        return $template;
    }
}
