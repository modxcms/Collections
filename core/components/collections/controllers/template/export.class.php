<?php
require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

/**
 * Create controller for Collections Container
 *
 * @package collections
 * @subpackage controller
 */
class CollectionsTemplateExportManagerController extends CollectionsBaseManagerController
{
    public function getLanguageTopics()
    {
        return array('collections:default');
    }

    public function process(array $scriptProperties = array())
    {
        $templateIDs = $_GET['ids'];
        $templateIDs = $this->collections->explodeAndClean($templateIDs);

        if (empty($templateIDs)) {
            die($this->modx->lexicon('collections.err.template_ns'));
        }

        /** @var CollectionTemplate[] $templates */
        $templates = $this->modx->getIterator('CollectionTemplate', array('id:IN' => $templateIDs));

        $fileContent = array();
        $fileName = '';

        foreach ($templates as $template) {
            $export = $template->toArray();
            unset($export['id'], $export['global_template']);
            $export['columns'] = array();

            foreach ($template->Columns as $column) {
                $exportColumn = $column->toArray();
                unset($exportColumn['id'], $exportColumn['template']);
                $export['columns'][] = $exportColumn;
            }

            $fileContent[] = $export;

            if (empty($fileName)) {
                $fileName = 'collection_view_' . strtolower($template->name);
            } else {
                $fileName = 'collection_views';
            }
        }

        if (empty($fileContent)) {
            die($this->modx->lexicon('collections.err.template_ns'));
        }

        session_write_close();
        ob_clean();

        header('Pragma: public');  // required
        header('Expires: 0');  // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Description: File Transfer');
        header('Content-Type:'); //added to fix ZIP file corruption

        header('Content-Type: "application/force-download"');
        header('Content-Disposition: attachment; filename="' . $fileName . '.json"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');

        echo json_encode($fileContent, JSON_PRETTY_PRINT);

        die();

    }

}