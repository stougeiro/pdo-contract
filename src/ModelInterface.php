<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use STDW\Contract\Pdo\QueryTree\QueryTreeBuilderInterface;
use STDW\Contract\Pdo\Result\ResultInterface;


/**
 * The developer-facing surface: a named table reached through arrays.
 *
 * Implementations declare $table and $connection, resolve the driver by
 * that connection name and lower the array DSL (filters, order,
 * pagination) to the neutral query tree through QueryTreeBuilderInterface
 * before reaching the driver; the driver only ever sees the IR. Values are
 * auto-wrapped into typed Params and a malformed DSL fails fast with
 * QueryTreeExceptionInterface. The driver returns a RawInterface, which
 * the model standardizes into a ResultInterface.
 *
 * Core surface only: the elective operations (search, stream, upsert,
 * batch, aggregate, transactions) are not part of this contract — the
 * implementation decorates the model with Traits (HasUpsert, HasBatchOps,
 * ...), so the model contract stays the stable CRUD minimum.
 *
 * The model is a spearhead: it exposes neither the driver nor the
 * connection. Lower-level needs are reached outside it — the Connection
 * for cross-model transactions and raw SQL — resolved by the
 * implementation's registry.
 *
 * @phpstan-import-type Filter from QueryTreeBuilderInterface
 * @phpstan-import-type Order from QueryTreeBuilderInterface
 * @phpstan-import-type Pagination from QueryTreeBuilderInterface
 */
interface ModelInterface
{
    /**
     * The table this model is bound to.
     * 
     * @return string 
     */
    public function table(): string;

    /**
     * Inserts one record and returns it.
     *
     * The returned record reflects what the database stored — not just the
     * input — so columns the database fills (defaults, timestamps, generated
     * values) are included, whether the engine returns them natively
     * (RETURNING) or they are fetched back afterwards.
     *
     * @param array<string, mixed> $data
     * @return ResultInterface
     */
    public function create(array $data): ResultInterface;

    /**
     * Returns a single record matching the filter.
     *
     * @param Filter $filter
     * @return ResultInterface
     */
    public function read(array $filter): ResultInterface;

    /**
     * Returns the records matching the filter.
     *
     * @param Filter $filter
     * @param Order $order Map of column => direction, or a list of entries.
     * @param Pagination $pagination limit/offset, or a cursor.
     * @return ResultInterface
     */
    public function list(array $filter = [], array $order = [], array $pagination = []): ResultInterface;

    /**
     * Updates the records matching the filter.
     *
     * @param array<string, mixed> $data
     * @param Filter $filter
     * @return ResultInterface
     */
    public function update(array $data, array $filter): ResultInterface;

    /**
     * Deletes the records matching the filter.
     *
     * @param Filter $filter
     * @return ResultInterface
     */
    public function delete(array $filter): ResultInterface;
}
