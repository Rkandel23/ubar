# Introduction #

The retrieval of internationalized and expression evaluated message strings, as well as their management and display, is built into the framework. Three types of messages are supported, errors, warnings, and notices. They all function the same except for errors, and even then, only within the context of user input validation in [Submit Actions](SubmitActions.md). The function and use of messaging is detailed in the sections below.

### Message Display ###
Messages are retrieved as object collections in views or templates. Typical usage would be to display all errors, warnings, and notices somewhere in your template. An example of this retrieval appears below.
```
<?php if(hasErrors()){ ?>
    <div id="errorBox">
        The following errors occured:
        <ul>
        <?php
            $errors = getErrors();
            $warnings = getWarnings();
            $allErrors = array_merge($errors, $warnings);
            foreach($allErrors as $error){ ?>
                <li><?php echo $error->getMessage() ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
```

Messages may also be displayed inline if they are associated with a specific field. An example appears below.
```
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

### Message Lifecycle ###
In order to allow messages to be added in one action and displayed in another that you have been redirected to, messages live in the session. They are cleared out at the end of the next successfully rendered view.

### Message Creation ###
Messages may be added with a key and argument lookup or with raw messages strings. They may also be associated with input field names. For the purposes of this tutorial, I will use warnings as an example, showing first the raw usage, then with a property lookup.

  * Simple Warning - Calling `$this->addWarningSimple($myMessage);` inside of your action will add a warning message with the value `$myMessage`, as simple as that. You may also, optionally, associate it with an input field for use in inline message display.
  * Key and Args - Messages may be added such that the message string is retrieved from a properties file and arguments either substituted in or used for expression evaluation. See the [Localized Properties](LocalizedProperties.md) page for more detail. An example appears below.
```
$this->addWarning("blog.warning.failedBlogPost", array("error" => $dbManager->getLastError()));
```
  * Using Input Field - In order to make a message available for inline display, you merely need to associate it with an input field name for retrieval. For example, the code below may be used to make a message available for display as in the example in [Message Display](Messaging#Message_Display.md).
```
if (Str :: nullOrEmpty($this->contents)) {
    $this->addError("submitComment.error.noComment", null, 'contents');
}
```

### Convenience Methods ###
A number of convenience methods exist for the support of message usage. Rather than detail them all here, see the [PHPDocs](http://www.holisticmonkey.com/static/ubar/docs/index.html) for the [Action](http://www.holisticmonkey.com/static/ubar/docs/core/Action.html) class.