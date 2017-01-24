<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/23/2017
 * Time: 3:07 PM
 */

namespace Webarq\Manager\Uploader;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderManager
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var
     */
    protected $name;

    public function __construct(UploadedFile $file, $dir, $name = null)
    {
        $this->file = $file;
        $this->dir = $this->createDir($dir);
        $this->setName($name);
    }

    protected function createDir($path)
    {
        if ('/' !== $path) {
            $dirs = explode('/', strtolower($path));
            $path = '';
            foreach ($dirs as $dir) {
                if ('' === $dir) {
                    continue;
                }

                $path = trim($path . '/' . $dir, '/');

                if (!is_dir($path)) {
                    mkdir($path, 0755);
                }
            }
        }

        return $path ?: '/';
    }

    public function upload()
    {
        return $this->file->move($this->dir, $this->getName());
    }

    /**
     * @param bool|true $ext
     * @return mixed
     */
    public function getName($ext = true)
    {
        return $this->name . (true === $ext ? '.' . $this->file->getClientOriginalExtension() : '');
    }

    /**
     * Set file name
     *
     * @param null $name
     * @param bool $uniqueId
     */
    public function setName($name = null, $uniqueId = true)
    {
        if (null === $name) {
            $name = $this->file->getClientOriginalName();
            $name = substr($name, 0, strrpos($name, '.'));
        }
        $name = str_slug(strtolower($name));

        if (true === $uniqueId) {
            $name = uniqid() . '-' . $name;
        }

        $this->name = $name;
    }

    public function getPathName()
    {
        return $this->dir . '/' . $this->getName();
    }
}