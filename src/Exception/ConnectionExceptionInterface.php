<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * The physical link failed: opening or closing the connection, or running a
 * transaction (begin/commit/rollback) on it. Query execution failures are
 * DriverExceptionInterface instead.
 */
interface ConnectionExceptionInterface extends DatabaseExceptionInterface
{ }
