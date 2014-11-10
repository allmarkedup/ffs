<?php namespace Amu\Ffs;

use Symfony\Component\Finder\Finder as SymFinder;
use Symfony\Component\Finder\Adapter\GnuFindAdapter;
use Symfony\Component\Finder\Adapter\BsdFindAdapter;
use Amu\Ffs\Iterator\RecursiveDirectoryIterator;
use Amu\Ffs\Adapter\PhpAdapter;

class Finder extends SymFinder
{
    public function __construct($iteratorClass = null)
    {
        $this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;
        
        $this
            ->addAdapter(new GnuFindAdapter())
            ->addAdapter(new BsdFindAdapter())
            ->addAdapter(new PhpAdapter($iteratorClass), -50)
            ->setAdapter('php');
    }
}