<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 6:20 PM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;
use DB;
use Wa;

class ActivenessController extends BaseController
{
    public function actionGetIndex()
    {
        $id = $this->getParam(1);
        $activeness = (int)$this->getParam(2);

        if (is_numeric($id)) {
            $mgr = Wa::table($this->panel->getName());
            $row = DB::table($this->panel->getName())
                    ->select($mgr->primaryColumn()->getName())
                    ->where($mgr->primaryColumn()->getName(), $id)
                    ->get()
                    ->toArray();

            if ([] === $row) {
                return $this->actionGetForbidden();
            } else {
                $row = DB::table($this->panel->getName())
                        ->where($mgr->primaryColumn()->getName(), $id)
                        ->update(['is_active' => 1 === $activeness ? 0 : 1]);

                if ($row) {
                    $this->setTransactionMessage(Wa::trans('webarq.messages.success-update'), 'success');
                } else {
                    $this->setTransactionMessage(Wa::trans('webarq.messages.invalid-update'), 'warning');
                }
            }


        }

        return redirect(Wa::panel()->listingURL($this->panel));
    }
}