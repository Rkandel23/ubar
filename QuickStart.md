# Introduction #

This is the quickstart guide for the Ubar PHP MVC Framework. More information
can be found at the following locations:

## Resources ##

  * [Project Details](http://www.holisticmonkey.com/Framework.action)
  * [Docs](http://www.holisticmonkey.com/FrameworkDocs.action)
  * [Wiki](http://code.google.com/p/ubar/w/list)
  * [Source](http://code.google.com/p/ubar/source/checkout)
  * [Issues](http://code.google.com/p/ubar/issues/list)
  * [Downloads](http://code.google.com/p/ubar/downloads/list)
  * [Dev Group](http://groups.google.com/group/ubar-dev)

## Before You Start ##

First, you will need to download a copy of the framework. If you have not already done so, you can download one from the [Downloads](http://code.google.com/p/ubar/downloads/list) page (above) or checkout and build your own distribution using the SVN location listed on the [Source](http://code.google.com/p/ubar/source/checkout) page (above).

Requirements:
  * PHP 5.1 or greater. 5.3 is recommended but not required at this time.
  * Apache 2.2 or other web server.

Suggestions:
  * Mysql 5.0 or greater if you wish to use build in database tools.
  * PHPUnit 3.4 if you intend to use framework tools for unit testing.
  * Eclipse with phpeclipse is an ideal development environment.

INSTALLATION
  1. Set up the following folder structure, items marked with '`*`' are optional:
```
[web] (or [html] as is common in some hosting facilities)
	[css]*
	[images]*
	[js]*
	[static]*
	index.php (from the ubar [install] directory)
	.htdocs (from the ubar [install] directory)
[WEB-INF]
	[controller]
	[lib]
		[ubar]
			...
	[model]
	[properties]
	[sql]*
	[test]*
	[view]
```
  1. Configure your web server to use the [web](web.md) directory as the document root.
  1. Place the downloaded [ubar](ubar.md) directory where depicted in step 1.
  1. Move the files in ubar's [install](install.md) directory to the [web](web.md) directory as depicted in step 1.
  1. Edit ubar\_config.properties to override default values as necessary. Note that this is also where you add database configuration if you would like to use the built in database utilities.
  1. Verify the setup by following the "Hello World" guide. Alternatively you may use the wiki pages or the sample application included in the release to start development.
  * [Hello World Guide](http://code.google.com/p/ubar/wiki/HelloWorld)
  * [Wiki](http://code.google.com/p/ubar/w/list)