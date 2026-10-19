<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use STDW\Contract\Pdo\Exception\DriverExceptionInterface;
use STDW\Contract\Pdo\Result\RawInterface;
use STDW\Contract\Pdo\Statement\StatementInterface;


/**
 * The low-level normalizer: renders the executable IR through the dialect,
 * executes it over the connection and returns a RAW, engine-neutral result.
 *
 * One method only. The driver centralizes prepare and bind (ParamType ->
 * PDO::PARAM_*) and never interpolates a value into SQL. It also absorbs the
 * engine divergences that would otherwise leak upwards: identifier quoting,
 * operator rendering, LIMIT/OFFSET and the RETURNING-vs-last-insert-id split
 * for generated keys. The Model never branches on dialect — it builds a
 * StatementInterface and reads RawInterface back.
 *
 * The connection (its handle and transactions) and the dialect are internal
 * collaborators, not part of this surface. The driver stays IR-only: raw
 * SQL and cross-model transactions are reached through the Connection
 * (transactions and its PDO handle), not here.
 */
interface DriverInterface
{
    /**
     * Renders and executes the statement, returning the normalized result.
     *
     * @param StatementInterface $statement
     * @return RawInterface
     * @throws DriverExceptionInterface
     */
    public function execute(StatementInterface $statement): RawInterface;
}
