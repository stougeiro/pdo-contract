<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * The physical link failed: opening or closing the connection, running a
 * transaction (begin/commit/rollback) on it, or a raw query
 * (fetch/fetchAll/execute) it ran directly. SQL errors raised by the driver
 * on a translated statement are DriverExceptionInterface instead.
 */
interface ConnectionExceptionInterface extends DatabaseExceptionInterface
{ }
