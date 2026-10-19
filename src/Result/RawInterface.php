<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Result;


/**
 * Universal return of every Driver operation — the low-level, engine-neutral
 * outcome of an executed statement.
 *
 * The driver normalizes each engine's raw response here, so callers never
 * branch on dialect: a SELECT yields rows(), a write yields affected(), and
 * an insert yields the generated AUTO_INCREMENT key through insertedId()
 * regardless of whether the engine produced it via RETURNING or the native
 * last-insert-id. The Model standardizes this into a ResultInterface.
 *
 * @phpstan-type Row array<int|string, mixed>
 * @phpstan-type Rows array<int, array<int|string, mixed>>
 */
interface RawInterface
{
    /**
     * The rows produced by the statement (SELECT, or a write with returning()).
     *
     * @return Rows
     */
    public function rows(): array;

    /**
     * The number of rows affected by a write.
     * 
     * @return int 
     */
    public function affected(): int;

    /**
     * The AUTO_INCREMENT key created by an insert, when one was generated.
     * 
     * @return null|int|string 
     */
    public function insertedId(): null|int|string;
}
