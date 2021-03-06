# PhlocalDynamo

[![Build Status](https://travis-ci.org/meadsteve/phlocal-dynamo.svg?branch=master)](https://travis-ci.org/meadsteve/phlocal-dynamo)

This package provides a wrapper for the DynamoDb.jar that is available from AWS for local deployment.
It is intended to speed up development and testing with dynamo db.

The required dynamo library should be downloadable from here:  http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Tools.DynamoDBLocal.html

This package is a php port of a c# library from JUST EAT: https://github.com/justeat/LocalDynamoDb 

## Getting Started

Java is required as Amazon provide the local DynamoDb as a jar file.

Run ```composer require mead-steve/phlocal-dynamo```

The DynamoDb.jar isn't included in this package to avoid licensing issues.  
Add the contents of the zip/tar file from amazon in to ```vendor\MeadSteve\PhlocalDynamo\src\dynamo```

Then in any test classes simply do the following

```php
    $dynamo = new LocalDynamo(9091);
    $dynamo->start();
```

And to stop it
```php
    $dynamo->stop();
```

## Using  PhlocalDynamo

Calling ``` $dynamo->getClient()``` will return a dynamo client instance pointing to the local instance of dynamo.
