# Promise

Promise is a simple and lightweight implementation of Javascript Promises in PHP. Promises provide a clean and structured way to work with asynchronous operations, allowing you to handle deferred execution and manage asynchronous workflows more effectively.

## Features

- **Promises/A+ Compliant:** Follows the Promises/A+ specification for consistent behavior.
- **Chaining:** Chain promises together using the `then` method.
- **Error Handling:** Handle errors using the `catch` method.
- **Finalization:** Execute code regardless of the promise's state with the `finally` method.

## Getting Started

### Installation

```bash
composer require ucscode/promise
```

### Usage

```php
use Ucscode\Promise\Promise;

$promise = new Promise(function ($resolve, $reject) {
    // Asynchronous operation
    sleep(20);
    $resolve("Operation Successful");
});

$promise->then(
    fn ($value) => "Fulfilled: $value",
    fn ($reason) => "Rejected: $reason"
)
->finally(fn () => "Operation complete");
```

### Multiple Promises

The `Promise::all` method takes an array of promises, and collects their fulfilled values. If all promises are fulfilled, it resolves with an array of fulfilled values. If any promise is rejected, it rejects with the reason of the first rejected promise.

```php
$promises = [
    new Promise(function ($resolve) { $resolve(1); }),
    new Promise(function ($resolve) { $resolve(2); }),
    new Promise(function ($resolve) { $resolve(3); }),
];

Promise::all($promises)->then(
    function ($values) {
        // All promises fulfilled
        var_dump($values); // Output: array(1, 2, 3)
    },
    fn ($reason) => "At least one promise rejected"
);
```

### Contributing

Contributions are welcome! Feel free to open issues or pull requests.

### License

This project is licensed under the [MIT License](https://opensource.org/license/mit/)