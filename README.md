FFS
=====

Filesystem file finder and filterer with special support for YAML front-matter.

Built on top of the [Symfony Finder component](http://symfony.com/doc/current/components/finder.html).

```php
<?php

use Amu\Ffs\Finder;

$fs = new Finder(__DIR__);
foreach($fs->titleContains('hello') as $item) {
    echo $item->getFilename() . "<br>\n";
}

$fs = new Finder(__DIR__);
foreach($fs->idEquals("12345") as $item) {
    echo $item->getFilename() . "<br>\n";
}

```

