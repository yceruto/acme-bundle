Bundle directory structure for Symfony applications
===================================================

Short guide explaining how to migrate from the current directory structure 
to the new structure convention.

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

Let's start with the current structure (see `master` branch):

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

As you can see, the current structure is mixing the source code with resource files, configuration, documentation, etc.
which is not good enough as it is easy to get lost in large projects with tons of directories and files at the repository root.

**composer.json file:**

    "autoload": {
        "psr-4": {
            "Acme\\AcmeBundle\\": ""
        }
    }

Regarding autoloading, it has a minor impact when building optimized autoloaders with composer as the test classes will be in the classmap, 
that's not a huge deal if it's a few classes, but it's not zero-impact.

In the next sections we will know how to change this structure without breaking the bundle functionality.

#### Revamped Version

> The steps below refer to this sample repository, adjust them according to your case.

Before start, let's install the dependencies and run the tests to make sure everything is well.

    $ cd AcmeBundle
    $ composer install
    $ ./vendor/bin/simple-phpunit

First, creates a `src/` directory where all our source code will be placed:  

    $ mkdir src/
    $ mv AcmeBundle.php DependencyInjection/ Model/ Resources/ Service/ src/

and, rename the current `Tests/` directory to `tests/` (lowercase):  

    $ mv Tests/ tests/

Update now the `composer.json` to reflect the new PSR-4 autoload paths: 

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

Move `assets`, `bin` and `docs` directories at the root-level since they are not part of any Symfony directory convention:

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

> Pull Request https://github.com/symfony/symfony/pull/32845

It's compatible as of Symfony 4.4+ only where a new directory convention for bundles was introduced,
allowing you have `config/`, `public/`, `translations/` and `templates/` directories at the root.

This means that the `src/Resources/` directory is no longer needed:

    $ mv src/Resources/config config
    $ mv src/Resources/public public
    $ mv src/Resources/translations translations
    $ mv src/Resources/views templates
    $ rmdir src/Resources

At this time, the directory structure would look like this:

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
        
Consistent with [the standard PHP packages skeleton](https://github.com/php-pds/skeleton).

See it on [`upgraded`](https://github.com/yceruto/acme-bundle/tree/upgraded) branch.

License
-------

This software is published under the [MIT License](LICENSE.md)
