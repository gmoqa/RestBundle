MNC RestBundle
==============
Provides some utilties to rapidly build Restful API's.

> This bundle is only compatible with Symfony 4. We are working to make it compatible
for other LTS versions of Symfony.

You can check the documentation [here](/src/Resources/docs/0.intro.md), but frist
familiarize yourself with this readme.

## Features:
- Create RESTful endpoints in seconds with our awesome RestController
- Transformation/Serialization layer by `league/fractal`
- Json-Schema forms by `limenius/liform`
- Eager Load selectable Hydration
- Pagination at ORM level by `whiteoctober/pagerfanta`
- Easily control access to your resources implementing `OwnableInterface`
- Json Body Parser Listener
- Resource Managers for cleaning your controllers.
- Subresources route support

## Roadmap:
- Hypermedia Links Manager
- Advanced Collection Filtering
- Content Negotiation

## Install:

Simply run:

    composer require mnavarrocarter/rest-bundle
   
Then register the bundle in your `bundles.php`:

    // config/bundles.php
    
    <?php
    
    return [
        // ...
        MNC\Bundle\RestBundle\MNCRestBundle::class => ['all' => true],
    ];

## Configuration
This bundle does not need previous configuration in order to work.

## Requirements
In order to enable fast-devopment features is very recommended that you install 
the `symfony/maker-bundle` with composer.

## Usage

First, create a resource and give it a name.

    php bin/console make:resource post

Then, you should start writing your application logic, from your database seeding
to your fixtures, and other things.

To get a deep understanding on how this bundle works and what are the main parts
to it, read [the docs](/src/Resources/docs/0.intro.md).

## Credits
This bundle incorporates services definitions and code from these other bundles that
were extracted here to avoid dependency in other bundles.
- `limenius/liform-bundle`
- `samjarret/fractal-bundle`
