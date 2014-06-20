[![Build Status](https://travis-ci.org/michaelmoussa/maam.svg?branch=master)](https://travis-ci.org/michaelmoussa/maam)
[![Code Climate](https://codeclimate.com/github/michaelmoussa/maam.png)](https://codeclimate.com/github/michaelmoussa/maam)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/michaelmoussa/maam/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/michaelmoussa/maam/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/michaelmoussa/maam/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/michaelmoussa/maam/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/michaelmoussa/maam/v/stable.png)](https://packagist.org/packages/michaelmoussa/maam)

# (M)agic (A)ccessors (A)nd (M)utators

Spare yourself the tedium of writing and testing countless getters and setters by generating them automagically!

**Note:** Still in development. Use at your own risk, etc.

## Installation

The only supported method of installation is [Composer](https://getcomposer.org/). In fact, Maam requires Composer to
work its magic!

`composer require "michaelmoussa/maam:dev-master"`

## Usage

### Initializing

Including Maam in your project is extremely simple. When bootstrapping your application, just add these lines:

```
use Moose\Maam\Maam;

$loader = include '/path/to/vendor/autoload.php'; # Change this according to your application
$maam = new Maam(Maam::MODE_DEVELOPMENT);
$maam->init($loader);
```

First, we get an instance of Composer's `ClassLoader` by including the `vendor/autoload.php` script. Next, we create
an instance of the Maam initializer. Finally, we run the `->init(...)` method.

Maam requires three other bits of information to work, they are:

* The path to your application's source code.
* The path to where you want Maam to write the code it generates.
* The path to your Composer's autoload.php

If you do not specify these paths, Maam will use sane defaults, which are:

* Application source code: `<maam-directory>/../../../src`
  * Assuming a normal Composer installation, this would be the `src` directory in your project root.
* Generation path: `<maam-directory>/../../../cache/maam`
  * Assuming a normal Composer installation, this would be the `cache/maam` directory in your project root.
  * **Note:** THIS DIRECTORY MUST EXIST AND BE WRITABLE!
  * **Note:** `cache/maam/*` (or whatever you specify) should be in your `.gitignore`.
* Composer's autoload.php: `<maam-directory>/../../autoload.php`
  * Assuming a normal Composer installation, this would be the `autoload.php` two directories up from where Maam was installed.
  
Do those defaults not work for your application? No problem! Use the `setApplicationAutoloadPath()`, `setApplicationSourcePath()`, and/or `setGenerationPath()` methods before calling `->init()` to configure your own paths.

### Run modes

Maam has two modes - development and production. Production is the default, and you can override this by passing the mode to the constructor or by using the `->setMode()` method.

It is recommended that you specify the mode when initializing Maam based on what environment your script is running in. For example, supposing you had an `APPLICATION_ENV` constant that told your application where it was running, you could do something like this:

```
if (APPLICATION_ENV === 'development') {
    $maam = new Maam(Maam::MODE_DEVELOPMENT);
} else {
    $maam = new Maam(Maam::MODE_PRODUCTION);
}
```

#### MODE_DEVELOPMENT

This run mode tells Maam that it is running in a development environment where source files are likely to change
often. As such, it will re-scan and re-generate your source files whenever the initializer is run. This may
cause noticeable slowdown in your local development environment, depending on how many classes and Maam annotations
you have.

There are ways to minimize the overhead in doing this, and they will be implemented in this library in the future.

#### MODE_PRODUCTION

This is the run mode you should be using when your application is deployed to your production servers. This tells
Maam to expect that you have *already* generated the new source files. Of course, if you haven't already done so,
your application will encounter an error and/or behave unpredictably when it is run.

~~Generating the new source files manually is easy.~~

~~`/path/to/vendor/bin/maam.php /path/to/src`~~

~~Maam will generate your source files and let you know what it is adding to the classmap.~~

This was easy, but isn't anymore. :) It will be fixed. See #12 for status of this.

### Annotations

Maam works by using Annotations, which are special comments that you can place in phpDoc blocks. Maam currently has
three annotations - `@Getter`, `@Setter`, and `@Both`, which indicate that you need a getter, setter, or both a getter and setter generated. Let's consider this example file:

```
<?php
// /path/to/my-application/src/MyApplication/Person.php

namespace MyApplication;

use Moose\Maam\Annotation as Maam;

class Person
{
    /**
     * @Maam\Getter
     */
    protected $firstName;

    /**
     * @Maam\Setter
     */
    protected $lastName;

    /**
     * @Maam\Both
     */
    protected $dateOfBirth;
}
```

In this example, we're telling Maam that we want a Getter generated for the `firstName` property, a Setter generated
for the `lastName` property, and both a Getter and Setter generated for the `dateOfBirth` property. After running
`maam.php`, this script will be generated:

```
<?php
// /path/to/my-application/vendor/michaelmoussa/maam/generated/MyApplication/Person.php

namespace MyApplication;

use Moose\Maam\Annotation as Maam;

class Person
{
    /**
     * @Maam\Getter
     * @var string
     */
    protected $firstName;

    /**
     * @Maam\Setter
     */
    protected $lastName;

    /**
     * @Maam\Getter
     * @Maam\Setter
     */
    protected $dateOfBirth;

    /**
     * Sets the firstName.
     *
     * @param mixed $firstName
     * @return void
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Gets the lastName.
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the dateOfBirth.
     *
     * @param mixed $dateOfBirth
     * @return void
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * Gets the dateOfBirth.
     *
     * @return mixed
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }
}
```

41 lines of getter/setter code were generated for us, and all we needed were a few annotations!

## Bugs / Contributions

Found a bug? Thanks! Please [create an issue](https://github.com/michaelmoussa/maam/issues/new) with as much
information as possible, and I'll get it resolved as quickly as I can.

Is my documentation terrible? [create an issue](https://github.com/michaelmoussa/maam/issues/new) for that too and
tell me what you don't undersatnd, and I'll try to make it clearer.

Added something cool and want to send a Pull Request? Feel free! Just be sure to include unit tests for whatever
you add, and adhere to the [PSR2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
PHP coding standard.
