[![Build Status](https://travis-ci.org/michaelmoussa/maam.svg?branch=master)](https://travis-ci.org/michaelmoussa/maam)
[![Coverage Status](https://coveralls.io/repos/michaelmoussa/maam/badge.png)](https://coveralls.io/r/michaelmoussa/maam)

# (M)agic (A)ccessors (A)nd (M)utators

Spare yourself the tedium of writing and testing countless getters and setters by generating them automagically!

## Installation

The only supported method of installation is [Composer](https://getcomposer.org/). In fact, Maam requires Composer to
work its magic!

`composer require "michaelmoussa/maam:~0.1"`

## Usage

### Bootstrapping

Including Maam in your project is extremely simple. When bootstrapping your application, just add these three lines:

```
$loader = include __DIR__ . '/../vendor/autoload.php';
$bootstrap = new \Moose\Maam\Bootstrap();
$bootstrap->bootstrap($loader, '/path/to/src', \Moose\Maam\Bootstrap::MODE_DEVELOPMENT);
```

First, we get an instance of Composer's `ClassLoader` by including the `vendor/autoload.php` script. Next, we create
an instance of the Maam bootstrapper. Finally, we run the `->bootstrap(...)` method, which takes three parameters:

1. The instance of Composer's `ClassLoader`.
2. The path to your application's source code. Maam will scan these files for annotations indicating that you want
   getters and/or setters generated for properties.
3. The run mode, which can be either `Bootstrap::MODE_DEVELOPMENT` or `Bootstrap::MODE_PRODUCTION`.

#### MODE_DEVELOPMENT

This run mode tells Maam that it is running in a development environment where source files are likely to change
often. As such, it will re-scan and re-generate your source files whenever the bootstrap script is run. This may
cause noticeable slowdown in your local development environment, depending on how many classes and Maam annotations
you have.

There are ways to minimize the overhead in doing this, and they will be implemented in this library in the future.

#### MODE_PRODUCTION

This is the run mode you should be using when your application is deployed to your production servers. This tells
Maam to expect that you have *already* generated the new source files. Of course, if you haven't already done so,
your application will encounter an error and/or behave unpredictably when it is run.

Generating the new source files manually is easy.

`/path/to/vendor/bin/maam.php /path/to/src`

Maam will generate your source files and let you know what it is adding to the classmap.

### Annotations

Maam works by using Annotations, which are special comments that you can place in phpDoc blocks. Maam currently has
two annotations - `@Getter` and `@Setter`, which indicate that you need a getter or setter generated. Let's consider
this example file:

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
     * @Maam\Getter
     * @Maam\Setter
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
