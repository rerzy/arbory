[![Packagist](https://img.shields.io/packagist/v/arbory/arbory.svg)](https://packagist.org/packages/arbory/arbory)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/arbory/arbory/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/arbory/arbory/?branch=master)
[![Build Status](https://travis-ci.org/arbory/arbory.svg?branch=master)](https://travis-ci.org/arbory/arbory)
[![Coverage Status](https://coveralls.io/repos/github/arbory/arbory/badge.svg?branch=master)](https://coveralls.io/github/arbory/arbory?branch=master)

## Installation
#### Create new Laravel project
```bash
composer create-project --prefer-dist laravel/laravel my-project "5.6.*"
```

#### Go to project root
```bash
cd my-project
```

#### Require Arbory package
```bash
composer require arbory/arbory "0.2.*"
```

#### Fill in database info
```bash
vi .env
```

#### Run installer and follow instructions
```bash
php artisan arbory:install
```

## Usage

### Registering new pages

```php
Page::register( App\Pages\TextPage::class )
    ->fields( function( FieldSet $fieldSet )
    {
        $fieldSet->add( new Arbory\Base\Admin\Form\Fields\Richtext( 'text' ) );
    } )
    ->routes( function()
    {
        Route::get( '/', App\Http\Controllers\TextPageController::class . '@index' )->name( 'index' );
    } );
```

### Registering new admin modules

```php
Admin::modules()->register(  App\Http\Controllers\Admin\TextController::class );
```

### Working with nodes

The node repository is used to ensure that the website only displays active nodes to the user

```php
$currentNode = app( Arbory\Base\Nodes\Node::class );
$nodes = app( Arbory\Base\Repositories\NodesRepository::class ); 

// returns only the active children of the current node
$nodes->findUnder( $currentNode );
```

## Validation

[Validation rules](https://laravel.com/docs/5.6/validation) can be attached to any field, like so

```php
$form->addField( new Text( 'title' ) )->setRules( 'required' );
```

### Validating translations

```php
$form->addField( new Translatable( ( new Text( 'title' ) )->rules( 'required' ) ) );
```

### Custom validators

* arbory_require_one_localized - at least one translation exists for this field
* arbory_file_required - file has been uploaded or is being passed in request

## Fields

### Object Relation

Create a relation to another model 

```php
new Arbory\Base\Admin\Form\Fields\ObjectRelation( 'field_name', Arbory\Base\Nodes\Node::class );
```

To limit the amount of relations the user can select a third argument can be passed. Relation fields limited to a single model will be rendered more compactly.

```php
new ObjectRelation( 'field_name', Arbory\Base\Nodes\Node::class, 1 ); // single relation, compact view 
new ObjectRelation( 'field_name', Arbory\Base\Nodes\Node::class, 10 ); 
```

An optional depth parameter can be passed (automatically set for the node relation) which adds visual nesting to the field items

```php
( new ObjectRelation( 'field_name', Arbory\Base\Nodes\Node::class ) )->setIndentAttribute( 'depth' );
```

Items can be grouped by an attribute

```php
$getName = function( \Arbory\Base\Nodes\Node $model ) 
{
    return class_basename( $model->content_type );
};

( new ObjectRelation( 'field_name', Arbory\Base\Nodes\Node::class ) )->groupBy( 'content_type', $getName );
```

## Settings

Register a setting (with optional nesting) and retrieve it

```php
return [
    'my_letter' => [
        'to' => 'a friend',
        'subject' => 'Hello!'
    ]
]
```

```php
Settings::has('my_letter.to'); // true
Settings::get('my_letter.to'); // "a friend"
```

### Defining a field type

```php
return [
    'my_setting_key' => [
        'value' => 'My setting value',
        'type' => Arbory\Base\Admin\Form\Fields\CompactRichtext::class
    ],
]
```

### File settings

```php
return [
    'my_setting_file' => [
        'value' => null,
        'type' => Arbory\Base\Admin\Form\Fields\ArboryFile::class
    ],
    'my_setting_image' => [
        'value' => null,
        'type' => Arbory\Base\Admin\Form\Fields\ArboryImage::class
    ],
]
```

### Translatable settings

```php
return [
    'hello' => [
        'type' => Arbory\Base\Admin\Form\Fields\Translatable::class,
        'value' => [
            'type' => Arbory\Base\Admin\Form\Fields\CompactRichtext::class,
            'value' => [
                'en' => 'Hello',
                'lv' => 'Sveiks'
            ]
        ]
    ],
]
```

## Generators

### Quick generator

```bash
php artisan arbory:generate {type?} {--T|table=}
```

Generators available for

* Model
* Page
* Controller
* View
* AdminController - appends a new route to `routes/admin.php`

### Verbose Generator

```bash
php artisan arbory:generator
```

## Coding style

### JS

We use `airbnb` coding style for both JS and SASS (links below).

To install the built-in inspections for PHPStorm, follow these instructions:
[https://www.themarketingtechnologist.co/how-to-get-airbnbs-javascript-code-style-working-in-webstorm/](https://www.themarketingtechnologist.co/how-to-get-airbnbs-javascript-code-style-working-in-webstorm/) 

#### Note!

When specifying JSCS package in the configuration window, it has to be installed locally (within the project).
Global installation will not work (PHPStorm installs packages globally).

#### Customization

Rules can be modified either in separate files (`.jscsrc` or `.jscs.json` in project's root directory)
or project's `package.json` file (`jscsConfig` section).

#### Links:

* JS - [https://github.com/airbnb/javascript](https://github.com/airbnb/javascript)
* CSS / SASS - [https://github.com/airbnb/css](https://github.com/airbnb/css)

## Development

See [TODO.md](TODO.md) for more information.
