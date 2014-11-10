<?php namespace Amu\Ffs;

class SplFileInfo extends \SplFileInfo
{
    private $relativePath;

    private $relativePathname;

    protected static $idDelimiters = array('[', ']');

    public static function getIdFormat($match = null)
    {
        $match = $match ?: '([^\%1$s\%2$s]+)';
        return sprintf('/.*?\%1$s' . $match . '\%2$s.*/', static::$idDelimiters[0], static::$idDelimiters[1]);
    }

    public function __construct($file, $relativePath, $relativePathname)
    {
        parent::__construct($file);
        $this->relativePath = $relativePath;
        $this->relativePathname = $relativePathname;
    }

    public function getDepth()
    {
        return count(explode('/', $this->getRelativePathname())) - 1;
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

    public function getRelativePath()
    {
        return $this->relativePath;
    }

    public function getRelativePathname()
    {
        return $this->relativePathname;
    }

    public function getContents()
    {
        $level = error_reporting(0);
        $content = file_get_contents($this->getPathname());
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();
            throw new \RuntimeException($error['message']);
        }
        return $content;
    }
}