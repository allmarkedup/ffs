<?php namespace Amu\Ffs\Iterator;

use Amu\Ffs\SplFileInfo;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator as SymRecursiveDirectoryIterator;

class RecursiveDirectoryIterator extends SymRecursiveDirectoryIterator
{
    public function current()
    {
        return new SplFileInfo(parent::current()->getPathname(), $this->getSubPath(), $this->getSubPathname());
    }
}