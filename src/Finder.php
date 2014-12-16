<?php namespace Amu\Ffs;

use Symfony\Component\Finder\Finder as SymFinder;
use Symfony\Component\Finder\Adapter\GnuFindAdapter;
use Symfony\Component\Finder\Adapter\BsdFindAdapter;
use Amu\Ffs\Iterator\RecursiveDirectoryIterator;
use Amu\Ffs\Adapter\PhpAdapter;

class Finder extends SymFinder
{
    public function __construct($basePath, $ignoreDotFiles = true, $iteratorClass = null)
    {
        $this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;
        
        $this
            ->addAdapter(new GnuFindAdapter())
            ->addAdapter(new BsdFindAdapter())
            ->addAdapter(new PhpAdapter($iteratorClass), -50)
            ->setAdapter('php');

        $this->in($basePath)
            ->ignoreUnreadableDirs()
            ->ignoreDotFiles($ignoreDotFiles);
    }

    public function __call($name, $args)
    {
        $type = null;
        foreach(['DoesNotContain', 'DoNotContain', 'Contains', 'Contain', 'DoesNotEqual', 'DoNotEqual', 'Equals', 'Equal'] as $matchType) {
            if ( $this->endsWith($name, $matchType) ) {
                $type = $matchType;
                $key = str_replace($matchType, '', $name);
                break;
            }
        }
        if (!$type) {
            return;
        }
        $methodName = 'metadata' . $type;
        $args = array_merge([$key], $args);
        return call_user_func_array(array($this, $methodName), $args);
    }

    public function hasMetadata()
    {
        return $this->filter(function($file) {
            return $file->hasMetadata();
        });
    }

    public function metadataExists($key)
    {
        return $this->filter(function($file) use ($key) {
            return $file->metadataExists($key);
        });
    }

    public function metadataEquals($key, $value)
    {
        return $this->filter(function($file) use ($key, $value) {
            if ( $file->getMetadataValue($key) === $value ) {
                return true;
            }
            return false;
        });
    }

    // alias
    public function metadataEqual($key, $value)
    {
        return $this->metadataEquals($key, $value);
    }

    public function metadataDoesNotEqual($key, $value)
    {
        return $this->filter(function($file) use ($key, $value) {
            if ( $file->getMetadataValue($key) !== $value ) {
                return true;
            }
            return false;
        });
    }

    public function metadataDoNotEqual($key, $value)
    {
        return $this->metadataDoesNotEqual($key, $value);
    }

    public function metadataContains($key, $value)
    {
        return $this->filter(function($file) use ($key, $value) {
            if ( Finder::isIn($file->getMetadataValue($key), $value) ) {
                return true;
            }
            return false;
        });
    }

    public function metadataContain($key, $value)
    {
        return $this->metadataContains($key, $value);
    }

    public function metadataDoesNotContain($key, $value)
    {
        return $this->filter(function($file) use ($key, $value) {
            if ( ! Finder::isIn($file->getMetadataValue($key), $value) ) {
                return true;
            }
            return false;
        });
    }

    public function metadataDoNotContain($key, $value)
    {
        return $this->metadataDoesNotContain($key, $value);
    }

    public static function isIn($compare, $value)
    {
        if (is_array($compare)) {
            return in_array($value, $compare, is_object($value));
        } elseif (is_string($compare)) {
            if (!strlen($value)) {
                return empty($compare);
            }
            return false !== strpos($compare, (string) $value);
        } elseif ($compare instanceof Traversable) {
            return in_array($value, iterator_to_array($compare, false), is_object($value));
        }
    }

    public function pathname($pathname)
    {
        $info = pathinfo($pathname);
        $this->path($info['dirname']);
        $this->name($info['basename']);
        return $this;
    }

    protected function endsWith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }
}