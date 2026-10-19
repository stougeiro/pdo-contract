<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;


/**
 * Everything a driver needs to reach one database: the DSN and the
 * credentials/options that go with it.
 *
 * Configs are per dialect (MysqlConfig, SqliteConfig, ...). Each dialect's
 * value object declares its own required keys — host/port/schema for MySQL,
 * database for SQLite — and validates them on construction (from(array)),
 * guaranteeing the driver never receives a half-specified entry. The DSN
 * string is built there too.
 */
interface ConfigInterface
{
    /**
     * The full DSN string (e.g. "mysql:host=…;dbname=…" or "sqlite:/path").
     * 
     * @return string 
     */
    public function dsn(): string;

    /**
     * Credential, when the dialect needs one.
     * 
     * @return null|string 
     */
    public function username(): ?string;

    /**
     * Credential, when the dialect needs one.
     * 
     * @return null|string 
     */
    public function password(): ?string;

    /**
     * Native driver options (PDO attributes, charset, ...).
     *
     * @return array<array-key, mixed>
     */
    public function options(): array;
}
