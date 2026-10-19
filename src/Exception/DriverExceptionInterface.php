<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;


/**
 * A driver operation failed at execution time: connection error, SQL error,
 * or an elective operation the driver does not honor (a dialect gap must
 * never be mistaken for "no matches" — drivers throw, never yield empty).
 */
interface DriverExceptionInterface extends DatabaseExceptionInterface
{ }
