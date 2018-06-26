# sympla/the-brick

> We all wish we had that wise neighborhood bartender to give us advice over a few rounds.

This library helps to negotiate content related to eloquent models (fields, relations and filters)

## Installation

Install the package using composer:

    $ composer require sympla/the-brick ~1.0

That's it.

## Simple usage


```php
public function index(Request $request)
{
    $res = $res->negotiate('Models\User');
    return response()->json($res);
}
```

Create your filter 

```php
public function scopeFilterByPhone($query)
{
   return $query->where('phone', '<>', '');
}
```

Now you simply call your route with your filter and the fields you want to return in the request

```
http://localhost:8000/api/users?&fields=name,email&filters=filterByPhone
```

## Using with Laravel

### Service Provider (Optional on Laravel 5.5)
Once Composer has installed or updated your packages you need add aliases or register you packages into Laravel. Open up config/app.php and find the aliases key and add:

```
Sympla\Search\Search\SearchServiceProvider::class,
```

## Generating documentation

#### Documentation

```php
/**
 * This is a summary
 *
 * This is a description
 */

/**
 * This is a summary.
 * This is a description
 */

/**
 * @param1 this is param1.
 * @param2 this is param2.
 * @param3 this is param3.
 */

/**
 * @return this is a return.
 */
```

#### Docblock variables

* @negotiate : Informs which model the deal is using

### How use

Add to your docblock the documentation variables

#### Controller
```php
/**
 * Get and filter all users
 * 
 * @negotiate Models\User
 * @param1 Request $request
 * @return Json 
 */ 
public function index(Request $request)
{
    $res = $res->negotiate('Models\User');
    return response()->json($res);
}
```

#### Model
```php
/**
 * Get users with phones
 *
 * @param1 $query
 * @return Json
 */
public function scopeFilterByPhone($query)
{
   return $query->where('phone', '<>', '');
}
```

#### Generate the documentation

Execute this command

```bash
php artisan negotiate:docgen
```

#### Accessing the Documentation

Access the documentation through the url `http://localhost:8000/_negotiate/documentation`

## Contact

Bruno Coelho <bruno.coelho@sympla.com.br>

Marcus Campos <marcus.campos@sympla.com.br>

## License

This project is distributed under the MIT License. Check [LICENSE][LICENSE.md] for more information.