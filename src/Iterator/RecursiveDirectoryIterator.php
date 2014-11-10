<?php namespace Amu\Ffs\Iterator;

use Amu\Ffs\SplFileInfo;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator as SymRecursiveDirectoryIterator;

class RecursiveDirectoryIterator extends SymRecursiveDirectoryIterator
{
    protected $splFileInfoClass = 'Amu\Ffs\SplFileInfo';

    public function setFileInfoClass($splFileInfoClass)
    {
        $this->splFileInfoClass = $splFileInfoClass;
    }

    public function current()
    {
        return new $this->splFileInfoClass(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
    }
}