<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * A driver operation failed at execution time: a SQL error surfaced while
 * running the translated statement, or an operation the dialect does not
 * honor (a dialect gap must never be mistaken for "no matches" — drivers
 * throw, never yield empty). Failures of the physical link itself
 * (open/close/transaction) are ConnectionExceptionInterface instead.
 */
interface DriverExceptionInterface extends DatabaseExceptionInterface
{ }
