<?php namespace Amu\Ffs;

use Amu\Ffs\SplFileInfo;

class Filesystem
{
    protected $basePath;

    protected $finder;

    protected $iterator = null;

    protected $metaDataType;

    public function __construct($basePath, $iterator = null)
    {
        $this->basePath = $basePath;
        $this->setIterator($iterator);
    }
    
    public function listAll($depth = '>= 0')
    {
        return $this->getFinder()->depth($depth);
    }

    public function listDirectories($depth = '>= 0')
    {
        return $this->getFinder()->depth($depth)->directories();
    }

    public function listFiles($depth = '>= 0')
    {
        return $this->getFinder()->depth($depth)->files();
    }

    public function findById($id)
    {
        return $this->getFinder()->hasId($id); 
    }

    public function setIterator($iterator = null)
    {   
        $this->iterator = $iterator;
    }

    protected function getFinder()
    {
        $finder = new Finder($this->iterator);
        $finder->in($this->basePath)
            ->ignoreUnreadableDirs()
            ->ignoreDotFiles(true);
        return $finder;
    }

}