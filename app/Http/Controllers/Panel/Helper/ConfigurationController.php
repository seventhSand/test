<?php
/**
 * Created by PhpStorm
 * Date: 04/02/2017
 * Time: 17:23
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace App\Http\Controllers\Panel\Helper;


use App\Http\Controllers\Panel\BaseController;
use Illuminate\Support\Str;
use Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Validator;
use Wa;
use Webarq\Manager\Cms\HTML\Form\Input\FileInputManager;
use Webarq\Manager\Cms\HTML\FormManager;
use Webarq\Manager\HTML\Form\LaravelInputHint;
use Webarq\Model\ConfigurationModel;

class ConfigurationController extends BaseController
{
    use LaravelInputHint;

    /**
     * @var
     */
    protected $form;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $inputFiles = [];

    public function before()
    {
        parent::before();

        $options = $this->panel->getAction($this->action . '.form', []);

        if (is_string($options)) {
            $this->layout->{'rightSection'} = view($options);
        } else {
// Init form
            $this->form = Wa::html('form');

            if (is_callable($options)) {
                $options($this->form, $this->getValues());
            } else {
                $this->setFormAction($options);
                $this->setFormTitle($options);

                $options['module'] = $this->module;
                $options['panel'] = $this->panel;

                $this->form = Wa::manager('cms.html.form config', $this->admin, $options);
            }
        }
    }

    /**
     * Get form values
     *
     * @return array
     */
    protected function getValues()
    {
        $find = ConfigurationModel::where('module', $this->module->getName())
                ->where('key', '<>', '48d35125f4a3c2c005d5b0697463c4651704b427')
                ->get()
                ->toArray();
        if ([] !== $find) {
            foreach ($find as $row) {
                $rows[$row['key']] = is_numeric($row['setting'])
                        ? $row['setting'] : Str::decodeSerialize($row['setting']);
            }

            return $rows;
        }

        return [];
    }

    /**
     * Set form action
     *
     * @param array $options
     */
    protected function setFormAction(array & $options)
    {
        $action = array_pull($options, 'permalink');
        if (is_string($action)) {
            $action = \URL::panel($action);
        } else {
            $action = Wa::panel()->listingUrl($this->panel);
        }

        $options['action'] = $action;
    }

    /**
     * Set form title
     *
     * @param array $options
     */
    protected function setFormTitle(array &$options)
    {
        $options['title'] = array_pull($options, 'title', title_case($this->panel->getName() . ' edit'));

    }

    public function actionGetEdit()
    {
        $val = $this->getValues();

        $val['remote-value'] = Str::encodeSerialize($val);

        $this->form->setvalues($val);

        $this->form->addInput('hidden', 'remote-value');

        $this->form->compile();
    }

    public function actionPostEdit()
    {
        if (null !== $this->form) {
            $this->form->setvalues(Request::all());

            $this->form->compile();

            $validator = $this->validator($this->form);

            if (!$validator->fails()) {
                if ([] !== ($rows = $this->makeRows())) {

                    if ($this->saveRows($rows) || [] !== $this->inputFiles) {

                        $this->uploadFiles($this->inputFiles);

                        $this->setTransactionMessage(Wa::trans('webarq.messages.success-update'), 'success');

                        return redirect(Request::url());
                    } else {
                        $this->form->setAlert([trans('webarq.messages.invalid-update')], 'warning');
                    }
                }
            } else {
                $this->form->setAlert($validator->errors()->getMessages(), 'warning');
            }
        } else {
            return $this->actionGetForbidden();
        }
    }

    /**
     * @param FormManager $form
     *
     * @return mixed
     */
    protected function validator(FormManager $form)
    {
        return Validator::make(\Request::all(), $form->getValidatorRules(), $form->getValidatorMessages());
    }

    /**
     * @return array
     */
    protected function makeRows()
    {
        $inputs = $this->form->getInput();
        if ([] !== $inputs) {
            foreach ($inputs as $name => $input) {
                if ($input instanceof FileInputManager) {
                    $value = $this->inputFile($input);
                    if ([] === $value || null === $value) {
                        continue;
                    }
                } else {
                    $value = $input->getValue();
                }

                if (!is_null($input->{'modifier'})) {
                    $value = Wa::load('manager.value modifier')->{trim($input->{'modifier'})}($value);
                }
                $rows[$name] = $value;
            }

            return $rows;
        }

        return [];
    }

    /**
     * @param FileInputManager $input
     * @return mixed
     * @todo Testing array file input
     */
    protected function inputFile(FileInputManager $input)
    {
// File options
        $options = (array)$input->{'file'};
// Post File (s)
        $file = Request::file($input->getInputName());
        if (is_array($file)) {
            $result = [];
            foreach ($file as $key => $item) {
// Init uploader
                $uploader = $this->loadUploader(
                        array_get($options, 'type', 'file'),
                        $item,
                        array_get($options, 'upload-dir', '/'),
                        array_get($options, 'file-name'),
                        array_get($options, 'resize', [])
                );
// Push in to files
                $this->inputFiles[$input->getInputName()][] = $uploader;
// Push in to post
                $result[] = $uploader->getPathName();
            }
        } else {
            if (null === $file) {
                return null;
            }

// Init uploader
            $uploader = $this->loadUploader(
                    array_get($options, 'type', 'file'),
                    $file,
                    array_get($options, 'upload-dir', '/'),
                    array_get($options, 'file-name'),
                    array_get($options, 'resize', [])
            );
// Push in to files
            $this->inputFiles[$input->getInputName()] = $uploader;

            $result = $uploader->getPathName();
        }

        return $result;
    }

    /**
     * @param string $type
     * @param UploadedFile $file
     * @param string $dir
     * @param mixed $name
     * @param array $resize
     */
    protected function loadUploader($type, UploadedFile $file, $dir, $name = null, array $resize = [])
    {
        $manager = Wa::manager('uploader.' . $type . ' uploader', $file, $dir, $name)
                ?: Wa::manager('uploader. file uploader', $file, $dir, $name);

        if ([] !== $resize) {
            $manager->setResize($resize);
        }

        return $manager;
    }

    protected function saveRows(array $rows)
    {
        $update = false;

        foreach ($rows as $key => $value) {
            if (is_array($value)) {
                $value = Str::encodeSerialize($value);
            }
//  Find row
            $find = ConfigurationModel::whereModule($this->module->getName())
                    ->whereKey($key)
                    ->get()
                    ->toArray();

            if ([] === $find) {
                $rows[$key] = [
                        'module' => $this->module->getName(),
                        'key' => $key,
                        'setting' => $value,
                        'create_on' => Wa::load('manager.value modifier')->datetime()
                ];
            } else {
                unset($rows[$key]);

                if (ConfigurationModel::whereKey($key)
                        ->whereModule($this->module->getName())
                        ->update(['setting' => $value])
                ) {
                    $update = true;
                }
            }

            if ([] !== $rows) {

            }
        }
// Insert new row
        if ([] !== $rows) {
            $insert = ConfigurationModel::insert($rows);
        }

        return !empty($insert) || $update;
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
                if (!is_numeric($key) && isset($remote[$key]) && is_file($remote[$key])
                        && $file->getPathName() !== $remote[$key]
                ) {
                    unlink($remote[$key]);
                }
            }
        }

    }

    public function after()
    {
        if ($this->form instanceof FormManager) {
            $this->layout->{'rightSection'} = $this->form->toHtml();

            return parent::after();
        } else {
            return $this->actionGetForbidden();
        }
    }
}