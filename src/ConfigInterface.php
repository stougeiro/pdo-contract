<?php declare(strict_types=1);

namespace STDW\Contract\Pdo;


/**
 * Everything the Connection needs to reach one database: the DSN and the
 * credentials/options that go with it.
 *
 * Configs are per dialect (MysqlConfig, SqliteConfig, ...). Each dialect's
 * value object declares its own required keys — host/port/schema for MySQL,
 * database for SQLite — and validates them on construction (from(array)),
 * guaranteeing the Connection never receives a half-specified entry. The DSN
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

    /**
     * Whether the generated record should be returned on insert even when the
     * engine has no native RETURNING, by emulating it with a follow-up select.
     *
     * RETURNING is the default behavior of the solution and takes precedence:
     * on an engine that supports it natively this flag is irrelevant. It is
     * only ever consulted where RETURNING is unavailable (i.e. MySQL), and
     * defaults to true so the returned record is complete — including columns
     * the database fills (defaults, timestamps, generated values). Turn it off
     * when only the generated key is needed and the extra select is not worth
     * it.
     *
     * @return bool
     */
    public function emulateReturning(): bool;
}
