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
     * @var string
     */
    protected $layout = 'form';

    /**
     * Final post data
     *
     * @var array
     */
    protected $post = [];

    /**
     * URL segment for id
     *
     * @var number
     */
    protected $idSegment = 3;

    /**
     * @var
     */
    protected $model;

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
        if ('edit' === $this->action) {
            $options['action'] .= '/' . $this->getParam($this->idSegment);
        }

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
        $this->builder->setvalues(Request::all());

// Compile the builder
        $this->builder->compile();

// Initiate validator
        $validator = $this->validator();

        if (!$validator->fails()) {
//
            $data = Wa::manager('cms.query.post', 'create', $this->post, $this->builder->getPairs());

            $manager = Wa::manager('cms.query.insert', $this->admin, $data->getPost(), $this->builder->getMaster());

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

    /**
     * File uploader
     *
     * @param array $files
     */
    protected function uploadFiles(array $files)
    {
        $remote = Request::input('remote-value', []);
        if (!is_array($remote)) {
            $remote = unserialize(base64_decode($remote));
        }

        if ([] !== $files) {
            foreach ($files as $key => $file) {
// Upload file
                $file->upload();
// Unlink old file
                if (!is_numeric($key) && isset($remote[$key]) && is_file($remote[$key])) {
                    unlink($remote[$key]);
                }
            }
        }

    }

    /**
     * Fresh edit
     */
    public function actionGetEdit()
    {
        $this->builder->dataModeling($this->getParam($this->idSegment));

        $val = $this->builder->getValue();

        if (!is_array($val) || [] === $val) {
            return $this->actionGetForbidden();
        }

        $this->builder->compile();
    }

    /**
     * Handling submitted edit form
     */
    public function actionPostEdit()
    {
// Set master ID
        $this->builder->setEditingRowId($this->getParam($this->idSegment));

// Add post data in to form builder
        $this->builder->setvalues(Request::all());

// Compile the builder
        $this->builder->compile();

// Initiate validator
        $validator = $this->validator();

        if (!$validator->fails()) {
            $data = Wa::manager('cms.query.post', 'edit', $this->post, $this->builder->getPairs());

            $manager = Wa::manager('cms.query.update', $this->admin, $data->getPost(), $this->builder->getMaster())
                    ->setId($this->getParam($this->idSegment));

            $count = $manager->execute();
            if ($count) {
// File upload handling
                $this->uploadFiles($data->getFiles());
// Set messages
                \Session::flash('transaction', [[trans('webarq.messages.success-update')], 'success']);

                return redirect(Request::url());
            } else {
                $this->builder->setAlert([trans('webarq.messages.invalid-update')], 'warning');
            }
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
}