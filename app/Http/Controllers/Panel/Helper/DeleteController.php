<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/26/2017
 * Time: 11:21 AM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;
use DB;
use Wa;
use Webarq\Info\TableInfo;
use Webarq\Model\NoModel;

class DeleteController extends BaseController
{
    /**
     * @var mixed
     */
    protected $id;

    public function actionGetIndex()
    {
        $id = $this->getParam('1');
        $op = $this->panel->getAction('delete', []);
// Find row
        $cn = Wa::table($this->panel->getTable())->primaryColumn()->getName();
        $rw = NoModel::instance($this->panel->getTable($cn))
                ->where($cn, $id)
                ->first();

        if (null === $rw) {
            return $this->actionGetForbidden();
        } else {
            $rw = $rw->toArray();
            if ([] === ($rules = array_get($op, 'rules', []))
                    || Wa::manager('cms.rule', $this->admin, $rules, $rw, $this->panel->getTable())->isValid()
            ) {
                $tb = $this->compile(array_pull($op, 'tables', []));

                if ([] !== $tb) {
                    list($primary, $secondaries) = $tb;
                    list($table, $options) = $primary;
// Deleting some row
                    $mgr = Wa::table($table);
                    $this->checkSequenceColumn($mgr, $options);
                    $del = Wa::manager('cms.query.delete', $table, $options, [$rw], $mgr->primaryColumn()->getName())
                            ->delete(true);

                    if ($del) {
                        if ([] !== $secondaries) {
                            foreach ($secondaries as $table => $options) {
                                $this->checkSequenceColumn(Wa::table($table), $options);

                                Wa::manager('cms.query.delete', $table, $options,
                                        $this->getParam(1), $mgr->getReferenceKeyName())->delete(true);
                            }
                        }
                    }
// Set message to session
                    $this->setTransactionMessage(Wa::trans('webarq.messages.success-delete'), 'success');

                    if (null !== ($callback = array_get($op, 'callback')) && is_callable($callback)) {
                        return $callback($id);
                    }

                    return redirect(Wa::panel()->listingURL($this->panel));
                } else {
                    return $this->actionGetForbidden();
                }
            } else {
                return $this->actionGetForbidden();
            }
        }
    }

    /**
     * @param array $tables
     * @return array
     */
    protected function compile(array $tables)
    {
        if ([] !== $tables) {
            $primary = '';
            foreach ($tables as $table => $options) {
                if (is_numeric($table)) {
                    unset($tables[$table]);
                    $table = $options;
                    $options = [];
                    $tables[$table] = $options;
                }

                if ('' === $primary || 0 === strpos($primary, $table)) {
                    $primary = $table;
                }
            }

            return [[$primary, array_pull($tables, $primary, [])], $tables];
        }

        return [];
    }

    /**
     * @param TableInfo $table
     * @param array $options
     */
    protected function checkSequenceColumn(TableInfo $table = null, array &$options)
    {
        if (null !== $table && !isset($options['sequence-column']) && null !== $table->getSequence()) {
            $options['sequence-column'] = $table->getSequence()->getName();
        }
    }
}