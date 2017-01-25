<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 1:12 PM
 */

namespace Webarq\Manager\Cms\Query;


use Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wa;
use Webarq\Manager\Cms\HTML\Form\AbstractInput;

class PostManager
{
    /**
     * Form type, create or edit
     *
     * @var
     */
    protected $type;

    /**
     * Pre-defined post data
     *
     * @var array
     */
    protected $post = [];

    /**
     * @var array
     */
    protected $pairs = [];

    /**
     * @var array
     */
    protected $collections = [];

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @param $type
     * @param array $post
     * @param array $inputs
     * @param array $multilingualInputs
     */
    public function __construct($type, array $post, array $inputs, array $multilingualInputs)
    {
        $this->type = $type;
        $this->post = $post;

        $this->masterRow($inputs);

        $this->translationRow($multilingualInputs);
    }

    /**
     * Collect master data
     *
     * @param array $pairs
     */
    protected function masterRow(array $pairs)
    {
        if ([] !== $pairs) {
            foreach ($pairs as $input) {
                $value = $this->getValue($input);
// Value not set and should be ignored
                if ($this->isIgnored($input, $value)) {
                    continue;
                }
                if (is_array($value)) {
                    $this->multiRow($value, $input->{'table'}->getName(), $input->{'column'}->getName());
                } else {
                    $this->post[$input->{'table'}->getName()][$input->{'column'}->getName()] = $value;
                }
            }
        }
    }

    /**
     * @param AbstractInput $input
     * @return mixed
     */
    protected function getValue(AbstractInput $input)
    {
        if (($input->isPermissible())) {
            if (!is_null($input->{'file'})) {
                return $this->inputFile($input);
            } else {
                $value = $input->getValue();
            }
        } else {
            $value = $input->isIgnored() ? null : $input->getImpermissible();
        }

        if (!$this->isIgnored($input, $value) && !is_null($input->{'modifier'})) {
            if (is_array($value)) {
                foreach ($value as &$str) {
                    $str = Wa::load('manager.value modifier')->{trim($input->{'modifier'})}($str);
                }
            } else {
                $value = Wa::load('manager.value modifier')->{trim($input->{'modifier'})}($value);
            }
        }

        return $value;
    }

    /**
     * @param AbstractInput $input
     * @return mixed
     * @todo Testing array file input
     */
    protected function inputFile(AbstractInput $input)
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
                        array_get($options, 'resize', [])
                );
// Push in to files
                $this->files[] = $uploader;
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
                    array_get($options, 'resize', [])
            );
// Push in to files
            $this->files[] = $uploader;

            $result = $uploader->getPathName();
        }

        return $result;
    }

    /**
     * @param string $type
     * @param UploadedFile $file
     * @param string $dir
     * @param array $resize
     */
    protected function loadUploader($type, UploadedFile $file, $dir, array $resize = [])
    {
        $manager = Wa::manager('uploader.' . $type . ' uploader', $file, $dir)
                ?: Wa::manager('uploader. file uploader', $file, $dir);

        if ([] !== $resize) {
            $manager->setResize($resize);
        }

        return $manager;
    }

    /**
     * Check if given input is ignored or not
     *
     * @param AbstractInput $input
     * @param $value
     * @return bool
     */
    protected function isIgnored(AbstractInput $input, $value)
    {
        return Wa::getGhost() === $value
        || ($this->isEmpty($value) && $input->isIgnored())
        || $input->attribute()->has('readonly');
    }

    /**
     * Check if given value is empty or not
     *
     * @param $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return (!is_array($value) && ('' === trim($value) || null === $value)) || [] === $value;
    }

    /**
     * @param array $value
     * @param $table
     * @param $column
     */
    protected function multiRow(array $value, $table, $column)
    {
        foreach ($value as $key => $str) {
// @todo check translation for array row
            if (is_array($str)) {
                $str = base64_encode(serialize($str));
            }
            $this->post[$table][$key][$column] = $str;
        }
    }

    /**
     * Collect translation data
     *
     * @param array $inputs
     */
    protected function translationRow(array $inputs)
    {
        if ([] !== $inputs) {
            foreach ($inputs as $inputs) {
                foreach ($inputs as $code => $input) {
                    $t = \Wl::translateTableName($input->{'table'}->getName());
                    $value = $this->getValue($input);
                    if ($this->isIgnored($input, $value)) {
                        continue;
                    }
                    $this->post['translation'][$t][$code][$input->{'column'}->getName()] = $value;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}