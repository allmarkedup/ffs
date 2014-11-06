<?php namespace Amu\Ffs\Adapter;

use Symfony\Component\Finder\Adapter\PhpAdapter as SymPhpAdapter;
use Symfony\Component\Finder\Iterator;
use Amu\Ffs\Iterator\RecursiveDirectoryIterator;

class PhpAdapter extends SymPhpAdapter
{
    public function searchInDirectory($dir)
    {
        $flags = \RecursiveDirectoryIterator::SKIP_DOTS;

        if ($this->followLinks) {
            $flags |= \RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        }

        $iterator = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, $flags, $this->ignoreUnreadableDirs),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        if ($this->minDepth > 0 || $this->maxDepth < PHP_INT_MAX) {
            $iterator = new Iterator\DepthRangeFilterIterator($iterator, $this->minDepth, $this->maxDepth);
        }

        if ($this->mode) {
            $iterator = new Iterator\FileTypeFilterIterator($iterator, $this->mode);
        }

        if ($this->exclude) {
            $iterator = new Iterator\ExcludeDirectoryFilterIterator($iterator, $this->exclude);
        }

        if ($this->names || $this->notNames) {
            $iterator = new Iterator\FilenameFilterIterator($iterator, $this->names, $this->notNames);
        }

        if ($this->contains || $this->notContains) {
            $iterator = new Iterator\FilecontentFilterIterator($iterator, $this->contains, $this->notContains);
        }

        if ($this->sizes) {
            $iterator = new Iterator\SizeRangeFilterIterator($iterator, $this->sizes);
        }

        if ($this->dates) {
            $iterator = new Iterator\DateRangeFilterIterator($iterator, $this->dates);
        }

        if ($this->filters) {
            $iterator = new Iterator\CustomFilterIterator($iterator, $this->filters);
        }

        if ($this->sort) {
            $iteratorAggregate = new Iterator\SortableIterator($iterator, $this->sort);
            $iterator = $iteratorAggregate->getIterator();
        }

        if ($this->paths || $this->notPaths) {
            $iterator = new Iterator\PathFilterIterator($iterator, $this->paths, $this->notPaths);
        }

        return $iterator;
    }
}