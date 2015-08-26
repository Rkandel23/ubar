# Introduction #

Actions are the controller in this framework. They accomplish the following:
  * Assemble information for display
  * Provide access to assembled information within the view
  * Process user submitted input
  * Control flow
  * Manage errors and messages

Actions may be used in a variety of ways. For details about usage and pertinent methods, see the specific role pages below:
  * [View Actions](ViewActions.md) - Actions that assemble and expose data for a view.
  * [Submit Actions](SubmitActions.md) - Actions that accept user input, validate it, perform work on the input, send you to an appropriate view.
  * [Hybrid Actions](HybridActions.md) - Actions that have limited user input used to control display. For instance, having a GET parameter "itemId" used to retrieve that items information from the database for display.