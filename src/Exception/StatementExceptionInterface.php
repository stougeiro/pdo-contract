<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * The executable StatementInterface is malformed and was rejected before
 * reaching the engine: a write without data, an update/delete without a
 * WHERE, an insert carrying a query, returning outside a write. Fail-fast at
 * the statement edge — an unfiltered write is never silently allowed.
 */
interface StatementExceptionInterface extends DatabaseExceptionInterface
{ }
