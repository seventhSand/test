<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/31/2017
 * Time: 4:35 PM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;
use Wa;
use Webarq\Model\NoModel;

class ExportController extends BaseController
{

    public function actionGetIndex()
    {
        $rows = $this->getRows($this->getParam(1), $this->panel->getAction('export', []));

        if ([] !== $rows) {
            $fn = studly_case($this->panel->getName()) . '-' . date('M-d-y-s');
            // tell the browser it's going to be a csv file
            header('Content-Type: application/csv');
            // tell the browser we want to save it instead of displaying it
            header('Content-Disposition: attachment; filename="' . $fn . '.csv";');
            $this->streamDownload($rows);
        }

        return '';
    }

    protected function streamDownload(array $rows)
    {
// Create a file pointer
        $output = fopen('php://output', 'w');
// Headings
        fputcsv($output, array_keys($rows[0]));
// Content
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
    }

    /**
     * @param null $id
     * @param array $options
     * @return array
     */
    protected function getRows($id = null, array $options = [])
    {
// Check for model
        if (null !== ($var = array_get($options, 'model'))) {
            if (is_string($var)) {
                return Wa::model($var)->findExportData($id, $options);
            } elseif (is_callable($var)) {
                return $var($id, $options);
            }
        }

        $table = array_get($options, 'table', $this->panel->getTable());
        $model = NoModel::instance($table, Wa::table($table)->primaryColumn()->getName());

        if (is_numeric($id)) {
            $model = $model->select(array_get($options, 'columns', '*'))
                    ->where(Wa::table($table)->primaryColumn()->getName(), $id);
        } else {
            $model = $model->optionQueryBuilder($options);
        }

        return $model->get()->toArray();
    }
}