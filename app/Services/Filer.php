<?php

namespace App\Services;

class Filer
{
    protected $file_path = '';
    protected $file_type = 'tmp';
    protected $storage_type, $full_path;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage, $storage_local, $storage_cloud, $storage_tmp;

    /**
     * @var \Illuminate\Filesystem\FilesystemManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = app('filesystem');

        $this->storage_local = $this->manager->disk('local');
        $this->storage_cloud = $this->manager->disk(config('filesystems.cloud'));
        $this->storage_tmp = $this->manager->disk('tmp');

        $this->setStorage();
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function type($type)
    {
        $this->file_type = $type;
        $this->file_path = $type . DIRECTORY_SEPARATOR;

        if ($this->isLocal()) {
            $this->full_path = config('filesystems.disks.local.root');
        }

        $this->setStorage();

        if ($type == 'tmp' || $type == 'temp') {
            $this->file_path = '';
            $this->storage = $this->storage_tmp;
            $this->full_path = config('filesystems.disks.tmp.root');
        }

        return $this;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function path($file)
    {
        if ($this->isLocal()) {
            return $this->full_path . DIRECTORY_SEPARATOR . $this->file_path . $file;
        }

        return $this->file_path . $file;
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    public function size($file)
    {
        if ($this->has($file)) {
            return $this->storage->size($this->file_path . $file);
        }

        return false;
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    public function has($file)
    {
        return $this->storage->exists($this->file_path . $file) ? true : $this->sync($file);
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    public function get($file)
    {
        if ($this->has($file)) {
            return $this->storage->get($this->file_path . $file);
        }
        if ($this->sync($file)) {
            return $this->storage->get($this->file_path . $file);
        }

        return false;
    }

    /**
     * @param $file
     * @param $contents
     *
     * @return bool
     */
    public function put($file, $contents)
    {
        $saved = $this->storage->put($this->file_path . $file, $contents);

        if ($saved) {
            $this->sync($file);
        }

        return $saved;
    }

    /**
     * @param $source
     * @param $destination
     *
     * @return bool
     */
    public function copy($source, $destination)
    {
        $this->storage->copy($source, $this->file_path . $destination);
        $this->sync($destination);
    }

    /**
     * @param $source
     * @param $destination
     *
     * @return bool
     */
    public function move($source, $destination)
    {
        $this->storage->move($source, $this->file_path . $destination);
        $this->sync($destination);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function delete($file)
    {
        if ($this->file_type != 'tmp' && $this->shouldSync()) {
            $cloud_delete = false;
            if ($this->storage_cloud->exists($this->file_path . $file)) {
                $cloud_delete = $this->storage_cloud->delete($this->file_path . $file);
            }
            $local_delete = false;
            if ($this->storage_local->exists($this->file_path . $file)) {
                $local_delete = $this->storage_local->delete($this->file_path . $file);
            }

            return $cloud_delete || $local_delete;
        }

        if ($this->has($file)) {
            return $this->storage->delete($this->file_path . $file);
        }

        return false;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function sync($file)
    {
        if ($this->file_type == 'tmp') {
            return false;
        }

        if ($this->shouldSync()) {
            if ($this->storage_local->exists($this->file_path . $file)) {
                if ($this->storage_cloud->exists($this->file_path . $file)) {
                    return true;
                } else {
                    return $this->storage_cloud->put($this->file_path . $file, $this->storage_local->get($this->file_path . $file));
                }
            }
            if ($this->storage_cloud->exists($this->file_path . $file)) {
                if ($this->storage_local->exists($this->file_path . $file)) {
                    return true;
                } else {
                    return $this->storage_local->put($this->file_path . $file, $this->storage_cloud->get($this->file_path . $file));
                }
            }
        }

        return false;
    }

    protected function isLocal()
    {
        if ($this->storage_type == 'local' || $this->storage_type == 'localcloud') {
            return true;
        }

        return false;
    }

    protected function shouldSync()
    {
        if ($this->storage_type == 'localcloud') {
            return true;
        }

        return false;
    }

    private function setStorage()
    {
        switch (env('APP_STORAGE_DEFAULT', 'local')) {
            case 'local':
                $this->storage = $this->storage_local;
                $this->storage_type = 'local';
                break;
            case 'localcloud':
                $this->storage = $this->storage_local;
                $this->storage_type = 'localcloud';
                break;
            case 'cloud':
                $this->storage = $this->storage_cloud;
                $this->storage_type = 'cloud';
                break;
        }
    }
}
