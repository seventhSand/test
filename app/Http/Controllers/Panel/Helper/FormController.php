<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/15/2016
 * Time: 10:40 AM
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;
use Request;
use Session;
use Validator;
use Wa;
use Webarq\Manager\Cms\HTML\FormManager;

class FormController extends BaseController
{
    /**
     * @var object Webarq\Manager\Cms\HTML\FormManager
     */
    protected $builder;

    /**
     * Post data
     *
     * @var array
     */
    protected $post = [];

    protected $layout = 'form';

    public function before()
    {
        $parent = parent::before();

        if (isset($this->admin)) {
            $this->makeBuilder();
            $this->post = Request::input();
        }

        return $parent;
    }

    protected function makeBuilder()
    {
        $options = $this->panel->getAction($this->action . '.form', []);
        $options['action'] = \URL::panel(
                \URL::detect(
                        array_pull($options, 'permalink'), $this->module->getName(),
                        $this->panel->getName(), 'form/' . $this->action
                )
        );
        $options += [
                'module' => $this->module->getName(),
                'panel' => $this->panel->getName(),
                'type' => $this->action
        ];

        $this->builder = Wa::manager('cms.HTML!.form',\Auth::user(), $options, $this->action);
    }

    /**
     * User must choose either create or update
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function actionGetIndex()
    {
        return $this->actionGetForbidden();
    }

    /**
     * Fresh create
     */
    public function actionGetCreate()
    {
        $this->builder->compile();
    }

    /**
     * Handling submitted create form
     */
    public function actionPostCreate()
    {
// Add post data in to form builder
        $this->builder->setPost($this->post);

// Compile the builder
        $this->builder->compile();

// Initiate validator
        $validator = $this->validator();

        if (!$validator->fails()) {
            $manager = Wa::manager('cms.query.insert',
                    $this->admin,
                    $this->builder->finalizePost(),
                    $this->builder->getMaster());

            if ($manager->execute()) {
// Redirect to listing controller
                return redirect($this->panel->getPermalink('listing/index'));
            }
        } else {
            $this->builder->setAlert($validator->errors()->getMessages(), 'warning');
        }
    }

    /**
     * Create validator instance
     *
     * @return object Validator
     */
    protected function validator()
    {
        return Validator::make(
                $this->post, $this->builder->getValidatorRules(), $this->builder->getValidatorMessages()
        );
    }

    /**
     * Fresh edit
     */
    public function actionGetEdit()
    {
        $id = $this->getParam(3);
        if (is_numeric($id)) {

        }
        $this->builder->compile();
    }

    /**
     * Handling submitted edit form
     */
    public function actionPostEdit()
    {
        $this->builder->compile();
    }

    /**
     * @return mixed
     */
    public function after()
    {
        $this->layout->{'rightSection'} = $this->builder->toHtml();

        return parent::after();
    }
}