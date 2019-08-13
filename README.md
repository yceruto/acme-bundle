Upgrading directory structure of Symfony bundles
================================================

Short guide explaining how to upgrade your current directory structure 
to be consistent with standard skeletons:

    └── AcmeBundle
        ├── assets/
        ├── bin/
        ├── config/
        ├── docs/
        ├── public/
        ├── src/
        │   ├── Model/
        │   ├── Service/
        │   └── AcmeBundle.php
        ├── templates/
        ├── tests/
        └── translations/

AcmeBundle
----------

This is the current structure (see [`master`](https://github.com/yceruto/acme-bundle/tree/master) branch):

    └── AcmeBundle
        ├── DependencyInjection/
        ├── Model/
        ├── Resources/
        │   ├── assets/
        │   ├── bin/
        │   ├── config/
        │   ├── docs/
        │   ├── public/
        │   ├── translations/
        │   └── views/
        ├── Service/
        ├── Tests/
        ├── AcmeBundle.php
        └── composer.php

Even though it has worked since the beginning, this structure is mixing the source code with resource files, configuration, documentation, etc.
which is not good enough as it is easy to get lost in large bundles with tons of directories and files at the repository root.

**composer.json file:**

    "autoload": {
        "psr-4": {
            "Acme\\AcmeBundle\\": ""
        }
    }

Regarding autoloading, it has a minor impact when building optimized autoloaders with composer as the test classes will be in the classmap, 
that's not a huge deal if it's a few classes, but it's not zero-impact.

In the next sections we will change the structure to solve these issues without breaking the bundle functionality.

#### Revamped Version

> The following steps relate to this repository sample, adjust them to suit your case.

Before start, let's install the dependencies and run all tests to make sure everything were well.

    $ cd AcmeBundle
    $ composer install
    $ ./vendor/bin/simple-phpunit

Let's start by creating a directory `src/` and moving our source code there:

    $ mkdir src/
    $ mv AcmeBundle.php DependencyInjection/ Model/ Resources/ Service/ src/

We will then rename the current `Tests/` directory to `tests/` (lowercase):  

    $ mv Tests/ tests/

Update your the `composer.json` to reflect the new PSR-4 autoload paths: 

    "autoload": {
        "psr-4": {
            "Acme\\AcmeBundle\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Acme\\AcmeBundle\\Tests\\": "tests/"
        }
    }

> run `composer dump-autoload` to update the autoload files with the new psr-4 map.

and update the `phpunit.xml` and `phpunit.xml.dist` files as well:

    <testsuites>
        <testsuite name="AcmeBundle Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
    
    <filter>
        <whitelist>
            <directory>src</directory>
        </whitelist>
    </filter>

Move `assets/`, `bin/` and `docs/` directories at the root-level since they aren't part of any Symfony directory convention:

    $ mv src/Resources/assets/ src/Resources/bin/ src/Resources/docs/ ./

Finally, check if the tests have passed `./vendor/bin/simple-phpunit` if it's green, it means you're almost done!

This is how it should look so far:

    └── AcmeBundle
        ├── assets/
        ├── bin/
        ├── docs/
        ├── src/
        │   ├── DependencyInjection/
        │   ├── Model/
        │   ├── Resources/
        │   │   ├── config/
        │   │   ├── public/
        │   │   ├── translations/
        │   │   └── views/
        │   ├── Service/
        │   └── AcmeBundle.php
        └── tests/

Up to now, this structure is still compatible with all Symfony versions.

See it on [`revamped`](https://github.com/yceruto/acme-bundle/tree/revamped) branch.

#### Upgraded Version

This version will be supported since Symfony 4.4, where a new directory convention for bundles was introduced,
allowing you to have `config/`, `public/`, `translations/` and `templates/` directories at the root of your bundle.

It means that the `src/Resources/` directory is no longer needed, let's move these directories:

    $ mv src/Resources/config/ src/Resources/public/ src/Resources/translations/ ./
    $ mv src/Resources/views templates
    $ rmdir src/Resources

At this time, the directory structure would look like this (consistent with [the standard PHP packages skeleton](https://github.com/php-pds/skeleton)):

    └── AcmeBundle
        ├── assets/
        ├── bin/
        ├── config/
        ├── docs/
        ├── public/
        ├── src/
        │   ├── DependencyInjection/
        │   ├── Model/
        │   ├── Service/
        │   └── AcmeBundle.php
        ├── templates/
        ├── tests/
        └── translations/

But, to make this work properly, it is necessary to change the root path of the bundle:

    class AcmeBundle extends Bundle
    {
        public function getPath(): string
        {
            return \dirname(__DIR__);
        }
    }

See it on [`upgraded`](https://github.com/yceruto/acme-bundle/tree/upgraded) branch.

#### Mixed Version

If you want your bundle to be compatible with older versions of Symfony, use symlink as a workaround:

    $ mkdir src/Resources && cd src/Resources
    $ ln -s ../../config/ config
    $ ln -s ../../public/ public
    $ ln -s ../../translations/ translations
    $ ln -s ../../templates/ views

The final structure would be this one:

    └── AcmeBundle
        ├── assets/
        ├── bin/
        ├── config/
        ├── docs/
        ├── public/
        ├── src/
        │   ├── DependencyInjection/
        │   ├── Model/
        │   ├── Resources/
        │   │   ├── config/ (symlink)
        │   │   ├── public/ (symlink)
        │   │   ├── translations/ (symlink)
        │   │   └── views/ (symlink)
        │   ├── Service/
        │   └── AcmeBundle.php
        ├── templates/
        ├── tests/
        └── translations/

Last, let's define the bundle path according to the current Symfony version installed:

    use Symfony\Component\HttpKernel\Kernel;

    class AcmeBundle extends Bundle
    {
        public function getPath(): string
        {
            return Kernel::VERSION_ID >= 40400 ? \dirname(__DIR__) : __DIR__;
        }
    }
    
That's it!

See it on [`mixed`](https://github.com/yceruto/acme-bundle/tree/mixed) branch.

License
-------

This software is published under the [MIT License](LICENSE.md)
