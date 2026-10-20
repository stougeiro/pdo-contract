<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Statement\Type;


/**
 * The operation a StatementInterface carries.
 *
 * Neutral keys: the driver translates each kind to the engine's native SQL
 * (SELECT/INSERT/UPDATE/DELETE). Select reads; Insert/Update/Delete write
 * and therefore require data (and, for Update/Delete, a non-empty where).
 */
enum StatementKind: string
{
    case Select = 'select';
    case Insert = 'insert';
    case Update = 'update';
    case Delete = 'delete';
}
