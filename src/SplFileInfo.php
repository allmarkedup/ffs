<?php namespace Amu\Ffs;

use Symfony\Component\Yaml\Parser;

class SplFileInfo extends \SplFileInfo
{
    private $relativePath;

    private $relativePathname;

    private $rawContent = null;
    
    private $body = null;

    private $metadata = null;

    // protected static $idDelimiters = array('[', ']');

    protected static $frontMatterDelimiter = '---';

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
        return $this->getMetadataValue('id');
    }

    public function isHidden()
    {
        
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

    public function getMetadataValue($key)
    {
        $metadata = $this->getMetadata();
        return isset($metadata[$key]) ? $metadata[$key] : null;
    }

    public function getMetadata()
    {
        if ( is_null($this->metadata) ) {
            $this->parseRawContent();
        }
        return $this->metadata;
    }

    public function getBody()
    {
        if ( is_null($this->body) ) {
            $this->parseRawContent();
        }
        return $this->body;
    }

    public function getContents()
    {
        if ( is_null($this->rawContent) ) {
            $level = error_reporting(0);
            $content = file_get_contents($this->getPathname());
            error_reporting($level);
            if (false === $content) {
                $error = error_get_last();
                throw new \RuntimeException($error['message']);
            }
            $this->rawContent = $content;    
        }
        return $this->rawContent;
    }

    protected function parseRawContent()
    {
        $rawContent = $this->getContents();
        $lines = explode(PHP_EOL, $rawContent);
        if (count($lines) <= 1 || rtrim($lines[0]) !== '---') {
            $this->metadata = [];
            $this->body = $rawContent;
            return;
        }

        unset($lines[0]);
        $yaml = [];
        $parser = new Parser();
        $i = 1;

        foreach ($lines as $line) {
            if ($line === '---') {
                break;
            }
            $yaml[] = $line;
            $i++;
        }

        $this->metadata = $parser->parse(implode(PHP_EOL, $yaml));
        $this->body = implode(PHP_EOL, array_slice($lines, $i));
    }

    // public static function getIdFormat($match = null)
    // {
    //     $match = $match ?: '([^\%1$s\%2$s]+)';
    //     return sprintf('/.*?\%1$s' . $match . '\%2$s.*/', static::$idDelimiters[0], static::$idDelimiters[1]);
    // }

    public static function getFrontMatterValueMatcher($key, $value)
    {
        // return '/^' . $key . '\:\s+?' . $value . '\s+?' . static::$frontMatterDelimiter . '/m';
        return '/.*+\-\-\-/m';
    }

}