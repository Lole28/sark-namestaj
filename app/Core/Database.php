<?php

/**
 * Jedinstvena PDO konekcija ka MySQL bazi.
 * Svi upiti u aplikaciji idu kroz PDO pripremljene naredbe (zaštita od SQL injection-a).
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function veza(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $c = $GLOBALS['config']['db'];
        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset={$c['charset']}";

        try {
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            $poruka = $GLOBALS['config']['app']['debug']
                ? 'Baza nije dostupna: ' . $e->getMessage()
                : 'Baza podataka trenutno nije dostupna.';
            exit($poruka . "\n\nProveri da li je MySQL pokrenut u XAMPP-u i da li je uvezen fajl sql/sema.sql.");
        }

        return self::$pdo;
    }
}
