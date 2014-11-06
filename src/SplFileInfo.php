<?php namespace Amu\Ffs;

use Symfony\Component\Finder\SplFileInfo as SymSplFileInfo;

class SplFileInfo extends SymSplFileInfo
{
    protected static $idDelimiters = array('[', ']');

    public function getDepth()
    {
        return count(explode('/', $this->getRelativePathname()));
    }

    public function getId()
    {
        preg_match(static::getIdFormat(), $this->getFilename(), $matches);
        return count($matches) === 2 ? $matches[1] : null;
    }

    public function getBasenameWithoutExtension()
    {
        return $this->getBasename('.' . $this->getExtension());
    }

    public static function getIdFormat()
    {
        return sprintf('/.*?\%1$s([^\%1$s\%2$s]+)\%2$s.*/', static::$idDelimiters[0], static::$idDelimiters[1]);
    }
}