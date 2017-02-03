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
use Webarq\Info\PanelInfo;

class DeleteController extends BaseController
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var array
     */
    protected $options = [];

    public function actionGetIndex()
    {
        $this->id = $this->getParam('1');
        $this->options = $this->panel->getAction('delete');

        if (!is_numeric($this->id)) {
            return $this->actionGetForbidden();
        }

        if ([] !== ($tables = $this->compile()) && null !== ($p = array_pull($tables, 'primary'))) {
// Primary table is in top priority
            $mgr = Wa::table($p);
            $del = Wa::manager('cms.query.delete', $mgr, array_pull($tables, $p, []), $this->getParam(1))->delete();
// Primary deletion should be true
            if (false === $del) {
                return $this->actionGetForbidden();
            }

// Following the rest
            if ([] !== $tables) {
// @todo log sub deletion history
                foreach ($tables as $table => $columns) {
                    Wa::manager('cms.query.delete', Wa::table($table), $columns,
                            $this->getParam(1), $mgr->{'getReferenceKeyName'}())->delete(false);
                }
            }

            $this->setTransactionMessage(Wa::trans('webarq.messages.success-delete'), 'success');
        } else {
            return $this->actionGetForbidden();
        }

        return redirect(Wa::panel()->listingURL($this->panel));

    }

    protected function compile()
    {
// Primary table
        $primary = null;
// Get form create for input file
        $data = [];
        $inputs = $this->panel->getAction('create.form', []);
        if ([] !== $inputs) {
            $primary = array_get($inputs, 'master');
            foreach ($inputs as $input => $option) {
                if (is_numeric($input)) {
                    $input = $option;
                    $option = [];
                }
// Collect table
                if (str_contains($input, '.')) {
                    $a = explode('.', $input);
                    if (null === $primary || 0 === strpos($primary, str_singular($a[1]))) {
                        $primary = $a[1];
                    }
                    if (!isset($data[$a[1]])) {
                        $data[$a[1]] = [];
                    }
                }
// Check for media files
                if (isset($option['file'])) {
                    $data[$a[1]][] = $a[2];
                }
            }
// Mark primary data table
            $data['primary'] = $primary;
        }

        return $data;
    }
}