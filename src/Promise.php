<?php

namespace Ucscode\Promise;

class Promise
{
    private const PENDING = 'pending';
    private const FULFILLED = 'fulfilled';
    private const REJECTED = 'rejected';

    private string $state = self::PENDING;
    private mixed $result;
    private mixed $onFulfilled = null;
    private mixed $onRejected = null;

    public function __construct(callable $executor)
    {
        try {
            $executor(
                fn(mixed $value = null) => $this->resolve($value),
                fn(mixed $reason = null) => $this->reject($reason)
            );
        } catch (\Exception $e) {
            $this->reject($e);
        }
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null): self
    {
        $promise = new self(function() {});

        if ($this->state === self::FULFILLED) {
            $this->handleState($onFulfilled, $promise, 'resolve');
        } elseif ($this->state === self::REJECTED) {
            $this->handleState($onRejected, $promise, 'reject');
        } else {
            $this->onFulfilled = $onFulfilled;
            $this->onRejected = $onRejected;
        }

        return $promise;
    }

    public function catch(callable $onRejected): self
    {
        return $this->then(null, $onRejected);
    }

    public function finally(callable $onFinally): self
    {
        return $this->then(
            fn($value) => $onFinally() && $value,
            fn($reason) => $onFinally() && throw $reason
        );
    }

    public static function all(array $promises): Promise
    {
        return new self(function ($resolve, $reject) use ($promises) {
            $results = [];
            $count = count($promises);
            $fulfilledCount = 0;

            foreach ($promises as $index => $promise) {
                $promise->then(
                    function ($value) use (&$results, &$fulfilledCount, $index, $count, $resolve) {
                        $results[$index] = $value;
                        $fulfilledCount++;

                        if ($fulfilledCount === $count) {
                            $resolve($results);
                        }
                    },
                    function ($reason) use ($reject) {
                        $reject($reason);
                    }
                );
            }
        });
    }

    private function handleState(?callable $callback, self $promise, string $action): void
    {
        if ($callback) {
            try {
                $result = $callback($this->result);
                $promise->{$action}($result);
            } catch (\Exception $e) {
                $promise->reject($e);
            }
        } else {
            $promise->{$action}($this->result);
        }
    }

    private function resolve(mixed $value = null): void
    {
        if ($this->state === self::PENDING) {
            $this->state = self::FULFILLED;
            $this->result = $value;
            $this->handleState($this->onFulfilled, $this, 'resolve');
        }
    }

    private function reject(mixed $reason = null): void
    {
        if ($this->state === self::PENDING) {
            $this->state = self::REJECTED;
            $this->result = $reason;
            $this->handleState($this->onRejected, $this, 'reject');
        }
    }
}
