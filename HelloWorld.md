# Introduction #

This tutorial details three increasingly involved "hello world" examples. Note that you need to have completed [Installation](Installation.md) prior to attempting this tutorial. Also note that this assumes you are accessing your page through localhost on port 80. You may substitute any instance of "localhost" with your server name and port.

There is no explanation in this tutorial. It merely acts as a simple set of examples. More detail is available in the specific tutorials.


# Minimal "Hello World" #

  1. Create a new page, **_WEB-INF/view/pages/helloWorld.php_**
  1. Edit **_WEB-INF/view/pages/helloWorld.php_** to contain the following
```
Hello World
```
  1. Edit **_WEB-INF/lib/ubar/ubar.xml_** to add the following entry to the `<actions>` list.
```
<action name="HelloWorld" view="pages.helloWorld" />
```
  1. Request http://localhost/HelloWorld.action in your browser and confirm that you see the text, "Hello World"

# Action Based "Hello World" #

  1. Create a new php file, **_WEB-INF/view/pages/helloWorld.php_**
  1. Edit **_WEB-INF/view/pages/helloWorld.php_** to contain the following php code snippet
```
<?= get('helloWorld') ?>
```
  1. Create a new php file, **_WEB-INF/controller/HelloWorld.php_**
  1. Edit **_WEB-INF/controller/HelloWorld.php_** to contain the following php code
```
<?php
class HelloWorld extends Action {

	public function getHelloWorld() {
		return "Hello World";
	}

	public function executeInner() {
		return GlobalConstants::SUCCESS;
	}
}
?>
```
  1. Edit **_WEB-INF/lib/ubar/ubar.xml_** to add the following entry to the `<actions>` list.
```
<action name="HelloWorld" path="HelloWorld" view="pages.helloWorld" />
```
  1. Request http://localhost/HelloWorld.action in your browser and confirm that you see the text, "Hello World"

# Action Based "Hello World" With Message Lookup #

  1. Create a new php file, **_WEB-INF/view/pages/helloWorld.php_**
  1. Edit **_WEB-INF/view/pages/helloWorld.php_** to contain the following php code snippet
```
<?= get('helloWorld') ?>
```
  1. Create a file, **_WEB-INF/properties/resources.properties_**
  1. Edit **_WEB-INF/properties/resources.properties_** to contain the following
```
helloWorld.text.hi = Hello {who}
```
  1. Create a new php file, **_WEB-INF/controller/HelloWorld.php_**
  1. Edit **_WEB-INF/controller/HelloWorld.php_** to contain the following php code
```
<?php
class HelloWorld extends Action {

	private $helloWorldString;

	public function getHelloWorld() {
		return $this->helloWorldString;
	}

	public function executeInner() {
		$this->helloWorldString = $this->getProperties()->get('helloWorld.text.hi', array('who' => 'World'));
		return GlobalConstants::SUCCESS;
	}
}
?>
```
  1. Edit **_WEB-INF/lib/ubar/ubar.xml_** to add the following entry to the `<actions>` list.
```
<action name="HelloWorld" path="HelloWorld" view="pages.helloWorld" />
```
  1. Request http://localhost/HelloWorld.action in your browser and confirm that you see the text, "Hello World"

