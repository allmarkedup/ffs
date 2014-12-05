<?php namespace Amu\Ffs;

use Symfony\Component\Yaml\Parser;

class SplFileInfo extends \SplFileInfo
{
    private $relativePath;

    private $relativePathname;

    private $rawContent = null;
    
    private $body = null;

    private $metadata = null;

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

    public function hasMetadata()
    {
        return (! $this->isDir() && count($this->getMetadata()));
    }

    public function metadataExists($key)
    {
        if ( $this->isDir() ) {
            return false;
        }
        $metadata = $this->getMetadata();
        return isset($metadata[$key]);
    }

    public function getMetadataValue($key)
    {
        if ( $this->isDir() ) {
            return null;
        }
        $metadata = $this->getMetadata();
        return isset($metadata[$key]) ? $metadata[$key] : null;
    }

    public function getMetadata()
    {
        if ( $this->isDir() ) {
            return array();
        }
        if ( is_null($this->metadata) ) {
            $this->parseRawContent();
        }
        return $this->metadata;
    }

    public function getBody()
    {
        if ( $this->isDir() ) {
            return null;
        }
        if ( is_null($this->body) ) {
            $this->parseRawContent();
        }
        return $this->body;
    }

    public function getContents()
    {
        if ( $this->isDir() ) {
            return null;
        }
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
        if (count($lines) <= 1 || rtrim($lines[0]) !== static::$frontMatterDelimiter) {
            $this->metadata = [];
            $this->body = $rawContent;
            return;
        }

        unset($lines[0]);
        $yaml = [];
        $parser = new Parser();
        $i = 1;

        foreach ($lines as $line) {
            if ($line === static::$frontMatterDelimiter) {
                break;
            }
            $yaml[] = $line;
            $i++;
        }

        $this->metadata = $parser->parse(implode(PHP_EOL, $yaml));
        $this->body = implode(PHP_EOL, array_slice($lines, $i));
    }

}