# MongoDB Kohana module

This module is used to perform operations on the database Mongo.
CRUD operations are performed using the query builder

## Requirements

You must install mongo.so extension for PHP on linux, or mongo dll extension for
Windows

## Example usage:

### Select queries

```php
        $result = Mongo_DB::select(array('title', 'caption'))
                ->from('mydb', 'mycol')
                ->where(array('likes' => 100))
                ->execute()
                ->as_array();

```

```php
        $result = Mongo_DB::select()
                ->from('mydb', 'mycol')
                ->just_one()
                ->execute()
                ->current();

```

### Insert queries

```php
        $document = array(
            "title" => "Foo boo",
            "description" => "test description",
            "likes" => 10,
            "url" => "www.xxxx.com",
            "by", "Author"
        );

        Mongo_DB::insert('mydb', 'mycol', $document)
                ->execute();

```

### Update queries

```php
        $data = array(
            "title" => "FOO BOO"
        );

        Mongo_DB::update('mydb', 'mycol', $data)
                ->where(array("title" => "Foo boo"))
                ->multiple()
                ->execute();

```

### Delete queries

```php
        Mongo_DB::delete('mydb', 'mycol')
                ->where(array("likes" => 100))
                ->just_one()
                ->execute();

```

## Config

mongo.php

```php
return array
    (
    'default' => array
        (
        'host' => 'localhost',
        'port' => 27017,
        'default_database' => 'kohana',
        'default_collection' => 'kohana_collection'
    ),
);

```

