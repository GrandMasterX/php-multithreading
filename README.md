Multithreading class for php 5.3+
=================================
Multithreading class for php 5.3+

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist grandmasterx/yii2-multithreading "*"
```

or add

```
"grandmasterx/yii2-multithreading": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
$threads = new Threads;
$threads->newThread(dirname(__FILE__).'/file.php', array());
while (false !== ($result = $threads->iteration())) {
    if (!empty($result)) {
        echo $result."\r\n";
    }
}
echo (date("H:i:s"));
$end = microtime(true);
echo "Execution time ".round($end - $start, 2)."\r\n";
die;
```