LaraReport
============
[![Build Status](https://travis-ci.org/inpin/lara-report.svg?branch=master)](https://travis-ci.org/inpin/lara-report)
[![StyleCI](https://github.styleci.io/repos/135795948/shield?branch=master)](https://github.styleci.io/repos/135795948)
[![Maintainability](https://api.codeclimate.com/v1/badges/6032fc52d6dbc3d18f69/maintainability)](https://codeclimate.com/github/inpin/lara-report/maintainability)
[![Latest Stable Version](https://poser.pugx.org/inpin/lara-report/v/stable)](https://packagist.org/packages/inpin/lara-report)
[![Total Downloads](https://poser.pugx.org/inpin/lara-report/downloads)](https://packagist.org/packages/inpin/lara-report)
[![Latest Unstable Version](https://poser.pugx.org/inpin/lara-report/v/unstable)](https://packagist.org/packages/inpin/lara-report)
[![License](https://poser.pugx.org/inpin/lara-report/license)](https://packagist.org/packages/inpin/lara-report)

Trait for Laravel Eloquent models to allow easy implementation of a "user report" feature.

#### Composer Install (for Laravel 5.5 and above)

	composer require inpin/lara-report

#### Install and then run the migrations

```php
'providers' => [
    \Inpin\LaraReport\LaraReportServiceProvider::class,
],
```

```bash
php artisan vendor:publish --provider="Inpin\LaraReport\LaraReportServiceProvider" --tag=migrations
php artisan migrate
```

#### Setup your models

```php
class Book extends \Illuminate\Database\Eloquent\Model {
    use Inpin\LaraReport\Reportable;
}
```

#### Sample Usage

Firstly it needs to be seeded in `larareport_report_items` table.
```php
ReportItem::query()->create([
    'type'  => 'books',
    'title' => 'Price is incorrect',
]);
```

the `type` field is just for categorizing, I suggest to put your model morph name into it.

```php
// Create an empty report by currently logged in user.
$book->createReport();

// Create a report on $book object with "report item id" of 1 and 2, with message of null,
// and put current logged in user form default guard as reporter.
$book->createReport([1, 2]);

// Create a report on $book object with "report item id" of 1 and 2, and put user message of "some message on it",
// and put current logged in user form default guard as reporter.
$book->createReport([1, 2], 'some message');

// Create a report on $book object with "report item id" of 1 and 2, put user message of "some message on it",
// and put current logged in user form 'api' guard as reporter.
$book->createReport([1, 2], 'some message', 'api');

// Create a report on $book object with "report item id" of 1 and 2, put user message of "some message on it",
// and put $user (3rd param) as reporter.
$book->createReport([1, 2], 'some message', $user');

$book->reports(); // HasMany relation to reports of book.
$book->reports; // Collection of book's reports.

$book->isReported() // check if current logged in user form default guard has reported book.
$book->isReported // check if current logged in user form default guard has reported book.
$book->isReported('api') // check if current logged in user form 'api' guard has reported book.
$book->isReported($user) // check if '$user' has reported book.

$book->reportsCount; // return number of reports on $book.
$book->reportsCount(); // return number of reports on $book.
```

Report objects

```php
$report->assign(); // Assign current logged from default guard as admin of $report
$report->assign('api'); // Assign current logged from 'api' guard as admin of $report
$report->assign($user); // Assign $user as admin of $report

// set resolved_at with current timestamp and assign current logged from default guard as admin of $report
$report->resolve();

// set resolved_at with current timestamp and assign current logged from 'api' guard as admin of $report
$report->resolve('api');

// set resolved_at with current timestamp and assign $user as admin of $report
$report->resolve($user);

// check if $report is resolved or not.
$report->isResolved();
```

#### Credits

 - Mohammad Nourinik - http://inpinapp.com
