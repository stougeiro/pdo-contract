<?php declare(strict_types=1);

namespace STDW\Contract\Pdo\Result;


/**
 * Universal return of every Model operation.
 *
 * The driver returns raw PDO results; the Model standardizes them here.
 *
 * Semantics by operation:
 * - create/read: count() = 1 on success, 0 otherwise; data() = the record.
 * - list: count() = number of returned records; data() = the records.
 * - update/delete: count() = number of affected records; data() = null.
 * isValid() reports whether the operation itself succeeded.
 */
interface ResultInterface
{
    /**
     * The record(s) produced by the operation, when any.
     *
     * @return null|array<array-key, mixed>|object
     */
    public function data(): null|array|object;

    /**
     * The record/affected count semantics described above.
     * 
     * @return int 
     */
    public function count(): int;

    /**
     * Whether the operation itself succeeded.
     * 
     * @return bool 
     */
    public function isValid(): bool;
}
