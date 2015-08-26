# Introduction #

View actions are actions configured to assemble data and expose it to a view. There are two primary parts to a view action, the executeInner() method and public methods that begin with "get" or "is". A variety of other methods are exposed to the view to facilitate common site or application functionality. The pertinent parts are detailed below, as is a representative sample of a view action.

### Methods That Should Appear In Your Action ###
  * **executeInner()** - This method is run prior to rendering a view. In this context, it is used for assembling information for display and storing it in private variables.
  * **"get" and "is" methods** - These public methods are made available to the view associated with this action. They are retrieved in the view by calling get(methodname) where methodname is the method's name without the preceding "get" or "is".

### Methods Inherited from the Parent Action Class ###
  * **getProperties()** - Get your localized properties instance for message retrieval. Properties are described in more detail in the [Localized Properties](LocalizedProperties.md) section.
  * **getParam($key)** - Get a parameter from the action or template definition. These somewhat arbitrary parameters are described in the [Action Configuration](ActionConfiguration.md) section.


### Functions Made Available to the View ###
  * **get($methodName)** - Provides access to any public action method starting with "get" or "is".
  * **getTxt($key, $arguments)** - Gets a message from your localized properties file with the given key and makig use of the given arguments, if any. Note that the $arguments argument must be an array. See [Localized Properties](LocalizedProperties.md) for more information.
  * **getParam($key)** - Pass through access to the method described above in "Methods Inherited from the Parent Action Class"
  * **getActionName()** - Get the name of the current action.
  * **getActionClassName()** - Get the class name of the current action.
  * **getTitle()** - Get the page's title. This may be set manually, by overriding the public $title member, or by setting the title or titleKey properties for the action definition. For more information about action definitions, see the [Action Configuration](ActionConfiguration.md) section.
  * **getPage()** - Get the page name. This is the name of the current action.
  * **getSection()** - Get the section that this page belongs in. This is typically inherited from the template associated with the action definition for this view.
  * **getSubSection()** - Get the sub-section that this page belongs in. This is typically inherited from the template associated with the action definition for this view.


## Example Action ##
```
<?php
class Test extends Acton {

	private $bar = false;
	
	private $message;

	public function getFoo() {
		return "getFoo was called!";
	}
	
	public function isBar() {
		return $this->bar;
	}
	
	public function getMyMessage() {
		return $this->message;
	}

	public function executeInner() {
		// override the value of $bar
		$this->bar = true;
		
		// get a message from your resources.properties file with the key, 
		// testpage.text.myMessage, and using the value, 2, for {numFish} in the message
		$this->message = $this->getProperties()->get('testpage.text.myMessage', array('numFish' => 2));
		
		return GlobalConstants::SUCCESS;
	}
}
?>
```


## Associated Sample View ##
```
getFoo returns "<?= get('foo') ?>"<br />

The value for bar is <?= get('bar') ? 'true' : 'false' ?><br />

My Message is "<?= get('myMessage') ?>"
```


## Associated Sample View Rendered ##
```
getFoo returns "getFoo was called!"
The value for bar is true
My Message is "I have 2 fish."
```