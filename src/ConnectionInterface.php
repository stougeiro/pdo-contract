<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;

use PDO;


/**
 * The physical link to one database, built from a ConfigInterface.
 *
 * Lifecycle plus transaction control. A transaction is a property of the
 * underlying PDO connection, not of the driver, so it lives here:
 * begin()/commit()/rollback()/inTransaction() delegate straight to the
 * native handle. Sharing one Connection between Models is what makes a
 * transaction span multiple Models.
 *
 * Query execution lives on DriverInterface. pdo() exposes the live PDO
 * handle — this contract is PDO-based by design, so the native handle is
 * part of the core, not a family sub-interface.
 *
 * A failure to open/close the link or to run a transaction surfaces as
 * ConnectionExceptionInterface.
 */
interface ConnectionInterface
{
    /**
     * The config this connection was built with.
     * 
     * @return ConfigInterface 
     */
    public function config(): ConfigInterface;

    /**
     * Opens the physical connection. Idempotent.
     * 
     * @return void 
     */
    public function connect(): void;

    /**
     * Whether the physical link is currently open.
     * 
     * @return bool 
     */
    public function isConnected(): bool;

    /**
     * Closes the physical connection.
     * 
     * @return void 
     */
    public function disconnect(): void;

    /**
     * The live PDO handle. Connects lazily when needed.
     * 
     * @return PDO 
     */
    public function pdo(): PDO;

    /**
     * Opens a transaction on this connection. Throws when unsupported.
     * 
     * @return void 
     */
    public function begin(): void;

    /**
     * Commits the open transaction. Throws when unsupported.
     * 
     * @return void 
     */
    public function commit(): void;

    /**
     * Rolls back the open transaction. Throws when unsupported.
     * 
     * @return void 
     */
    public function rollback(): void;

    /**
     * Whether a transaction is currently open on this connection.
     * 
     * @return bool 
     */
    public function inTransaction(): bool;
}
