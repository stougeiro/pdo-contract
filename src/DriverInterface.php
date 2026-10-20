<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use STDW\Contract\Pdo\Exception\DriverExceptionInterface;
use STDW\Contract\Pdo\Result\RawInterface;
use STDW\Contract\Pdo\Statement\StatementInterface;


/**
 * The IR translator: renders the neutral, executable statement through the
 * dialect and returns a RAW, engine-neutral result.
 *
 * One method only, and it is IR-only by design: it compiles a
 * StatementInterface (SELECT/INSERT/UPDATE/DELETE plus the shared query
 * tree) into SQL, delegating the actual execution to the Connection — which
 * is the single place that talks to the native PDO handle. The translator
 * never interpolates a value (the Connection binds each Param) and absorbs
 * the engine divergences that would otherwise leak upwards: identifier
 * quoting, operator rendering, LIMIT/OFFSET and the RETURNING-vs-last-id
 * split for generated keys. The Model never branches on dialect — it builds
 * a StatementInterface and reads RawInterface back.
 *
 * Raw SQL is not translated, so it is not here: it lives on the Connection
 * (fetch/fetchAll/execute), reached by callers who own the SQL. The
 * connection (handle and transactions) and the dialect are internal
 * collaborators, not part of this surface.
 */
interface DriverInterface
{
    /**
     * Translates and executes the neutral statement, returning the
     * normalized result.
     *
     * @param StatementInterface $statement
     * @return RawInterface
     * @throws DriverExceptionInterface
     */
    public function translate(StatementInterface $statement): RawInterface;
}
