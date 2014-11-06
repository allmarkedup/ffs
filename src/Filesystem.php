<?php namespace Amu\Ffs;

class Filesystem
{
    protected $basePath;

    protected $finder;

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    public function listAll()
    {
        return $this->getFinder();
    }

    public function listDirectories()
    {
        return $this->getFinder()->in($this->basePath)->directories();
    }

    public function findById($id)
    {
        return $this->getFinder()->name('/.*?\[' . $id . '\][-.].*/');
    }

    public function getFinder()
    {
        $finder = new Finder();
        $finder->in($this->basePath)
            ->ignoreUnreadableDirs()
            ->ignoreDotFiles(true)
            ->sortByName();
        return $finder;
    }

}