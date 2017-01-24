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

    /**
     * @var string
     */
    protected $layout = 'form';

    /**
     * Row ID for editing
     *
     * @var number
     */
    protected $id;

    public function before()
    {
        $parent = parent::before();

        if (isset($this->admin)) {
            $this->makeBuilder();
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

        $this->builder = Wa::manager('cms.HTML!.form', \Auth::user(), $options, $this->action);
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
        $this->builder->setvalues(Request::input());

// Compile the builder
        $this->builder->compile();

// Initiate validator
        $validator = $this->validator();

        if (!$validator->fails()) {
            $data = Wa::manager('cms.query.post', 'create',
                    $this->post,
                    $this->builder->getPairs(),
                    $this->builder->getCollection('multilingual-frm-input')
            );

            $manager = Wa::manager('cms.query.insert',
                    $this->admin,
                    $data->getPost(),
                    $this->builder->getMaster());

            if ($manager->execute()) {
// File upload handling
                $this->uploadFiles($data->getFiles());
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
                \Request::all(), $this->builder->getValidatorRules(), $this->builder->getValidatorMessages()
        );
    }

    protected function uploadFiles(array $files)
    {
        if ([] !== $files) {
            foreach ($files as $file) {
                $file->upload();
            }
        }

    }

    /**
     * Fresh edit
     */
    public function actionGetEdit()
    {
        $this->builder->setValuesFromDB($this->id ?: $this->getParam(3));

        $this->builder->compile();
    }

    /**
     * Handling submitted edit form
     */
    public function actionPostEdit()
    {

// Add post data in to form builder
        $this->builder->setvalues(Request::input());

// Compile the builder
        $this->builder->compile();

// Initiate validator
        $validator = $this->validator();

        if (!$validator->fails()) {

        } else {
            $this->builder->setAlert($validator->errors()->getMessages(), 'warning');
        }
    }

    /**
     * @return mixed
     */
    public function after()
    {
        $this->layout->{'rightSection'} = $this->builder->toHtml();

        return parent::after();
    }

    protected function makePost()
    {

    }
}