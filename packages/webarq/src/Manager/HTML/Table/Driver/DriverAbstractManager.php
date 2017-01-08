<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/6/2017
 * Time: 4:16 PM
 */

namespace Webarq\Manager\HTML\Table\Driver;


abstract class DriverAbstractManager
{
    /**
     * There is two ways to provided data value:
     *
     * $data = [['id' => 1, 'name' => 'John Doe'], ['id' => 2, 'name' => 'Jane Doe'], ['name' => '??', 'id' => '??']]
     *
     * or
     *
     * $data = [
     *      'head' => ['id', 'name' => [some html attributes]],
     *      'rows' => [
     *          [1, 'John Doe']
     *          [2, 'Jane Doe']
     *          ['??' , '??']
     *      ]
     *
     * ]
     *
     * @var array
     */
    protected $data = [];

    /**
     * @param mixed $key
     * @return array
     */
    public function getData($key = null)
    {
        if ([] === $this->data && null !== ($rows = $this->getRows()) && is_array($rows) && [] !== $rows) {
            foreach ($rows as $iteration => $row) {
                foreach ($row as $column => $value) {
                    if (!isset($this->data['head'][$column])) {
                        $this->data['head'][$column] = $column;
                    }
                    $this->data['rows'][$iteration][$column] = $value;
                }
            }
        }

        return isset($key) ? array_get($this->data, $key, []) : $this->data;
    }

    /**
     * Get data rows
     *
     * @return mixed
     */
    abstract protected function getRows();

    /**
     * Provide sampling data
     *
     * @return mixed
     */
    abstract protected function sampling();
}