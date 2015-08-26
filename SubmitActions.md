# Introduction #

Submit actions are actions configured to process and validate user input. While it is possible to associate a submit action with a view, it is NOT recommended. Actions that mix limited submission and views are called [Hybrid Actions](HybridActions.md).

There are a number of elements to a submit action when used properly.

  * **Setters** - Functions used to assign user input to local attributes
  * **User Input Validation** - Validation of user input for completeness and easily validated
    * **Adding Errors** - Add problem messages that block execution of the action.
    * **[Redirection To Appropriate Page to Handle User Error](ResultHandling.md)** - Return different results based on the error conditions and allow the dispatcher to direct you to the appropriate action.
    * **Re-population Of Input Values** - If there's a problem with user input, indicate the problem but also re-populate form fields with the offending values.
  * **Error Display** - Test for, retrieve and display errors in the error view.
  * **Action Execution** - Perform work on the user submitted input.
  * **[Add Messages](Messaging.md)** - Add notice type messages to be displayed in the next view.
  * **Getters for Action Results** - Provide getters for appropriate action redirection. For example, if you edit a forum post, you want to return to that post after edit. To that end, it's useful to have access to the post id in the result.

A variety of methods support this functionality either in the base Action or through the Dispatcher. The pertinent parts required for the generation of a submit action are detailed below, as is a representative sample of a of this action type and expected outcomes.


### Methods That Should Appear In Your Action ###
  * **set{propertyname}()** - These are the methods that accept user input. The input may be in GET or POST form. The methods must be public and follow the naming convention "get" + capitalized property name. It is recommended that these methods are ONLY used to set private variables. Any validation of submitted data should occur in validateUserInput() due to built in exception handling and flow control. Examples of setters appear below.
```
private $postTitle;
private $contents;

public function setPostTitle($title) {
    $this->postTitle = $title;
}

public function setContents($contents) {
    $this->contents = $contents;
}
```
  * **validateUserInput()** - This method should contain any validation on user submitted data or request characteristics. Any errors added at this point will prevent further processing of the action and the evaluation of the result associated with the USER\_INPUT type. For more information about result handling, see the [SubmitActions#Result\_Handling](SubmitActions#Result_Handling.md) section. For more information about about message handling (errors, warnings, notices), see [Message Handling](MessageHandling.md). An example of user input validation appears below.
```
public function validateUserInput() {
    if(!$this->isAdmin()) {
        $this->addError("generic.error.insufficientPermissions");
    }
    if(Str::nullOrEmpty($this->postTitle)) {
        $this->addError("generic.error.missingRequiredField", array("field" => "postTitle"));
    }
    if(Str::nullOrEmpty($this->contents)) {
        $this->addError("generic.error.missingRequiredField", array("field" => "contents"));
    }
}
```
  * **executeInner()** - This is the method that performs the work of the action. In the context of submit actions, it is expected that this work be performed with the information gathered with the setters detailed above. An example appears below.
```
public function executeInner() {
    // instantiate db
    $dbManager = new DBManager();

    // escape strings for insert
    $title = $dbManager->escapeString($this->postTitle);
    $contents = $dbManager->escapeString($this->contents);

    // do query
    $result = mysql_query("INSERT INTO blog SET title='$title', message='$contents'");

    // check if successful
    if($result) {
        $this->addNotice("blog.notice.blogPosted");
    } else {
        $this->addError("blog.error.failedBlogPost", array("error" => $dbManager->getLastError()));
    }

    // return success regardless since returned to the same place and error displayed
    return GlobalConstants::SUCCESS;
}
```

### Error Handling ###
Errors (error type messages) play a special role in submit type actions. In the if Action::addError() is called inside of your action class's validateUserInput() method, the action's executeInner() method is not run and whatever result associated with USER\_INPUT is evaluated. For more information about result evaluation, see the [Result Handling](ResultHanlding.md) page. Errors messages behave as other types of messages, and more detail is available on the [Messaging](Messaging.md) page, but there is more to error handling than just message display.

  * **Errors block action execution** - As mentioned previously, an error added in validateUserInput() blocks the evaluation of the action's executeInner() action and evaluates the result associated with USER\_INPUT.
  * **Errors may have a field context** - Each message may be associated with an input field for the display of inline messages. For instance, if you have a form that asks how many hot dogs someone wants to purchase and you receive a value of "-1", you may create an error indicating the number is out of range and display that error message next to the "Num Hotdogs" field. An example of adding an error in this manner appears below.
```
if (Str :: nullOrEmpty($this->contents)) {
    $this->addError("submitComment.error.noComment", null, 'contents');
}

and in the view...

<?php if(hasErrorsOrWarningsForField('contents')) {
   $messages = getErrorsOrWarningsForField('contents');
?>
    <div class="inlineError">
        <ul>
        <?php foreach($messages as $message) { ?>
            <li><?php echo $message->getMessage() ?></li>
        <?php } ?>
        </ul>
    </div>
<?php } ?>
```
  * **Original "bad" input can be retrieved on next view** - In most cases, when user submitted data triggers an error, you will want to display the error AND re-populate the form with the invalid user input for correction. Just call getUserInput(fieldname) in the resulting view to retrieve the data. An example appears below.
```
<textarea rows="5" cols="50" name="contents"><?php echo getUserInput("contents") ?></textarea>
```

### Result Handling ###
Submit actions, being dependent on user submitted data, have a higher degree of variability than other actions. However, they are no different in the way they work. See the [Result Handling](ResultHandling.md) page for more information.

### Getters ###
The role of getters in submit actions is fairly specific given that you should not have an associated view. They should be used exclusively in results for the redirection to urls containing get params such as ids. They may referenced in result values by surrounding the property name with curly braces. An example of this usage in the ubar.xml config appears below.
```
<action name="PostBlogComment" path="blog.PostBlogComment">
    <results>
        <result type="url">Blog.action?blogId=${blogId}</result>
        <result name="USER_ERROR" type="url">Blog.action?blogId=${blogId}#comments</result>
    </results>
</action>
```<action name="PostBlogComment" path="blog.PostBlogComment">
    <results>
        <result type="url">Blog.action?blogId=${blogId}</result>
        <result name="USER_ERROR" type="url">Blog.action?blogId=${blogId}#comments</result>
    </results>
</action>
}}}```