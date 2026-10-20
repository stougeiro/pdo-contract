<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Exception;

use Throwable;


/**
 * Root marker of the package. Every thrown exception — connection setup,
 * driver execution, invalid query IR or malformed statement — is catchable
 * through this type.
 */
interface DatabaseExceptionInterface extends Throwable
{ }
