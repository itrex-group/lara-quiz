<?php
declare(strict_types=1);

namespace LaraQuiz\Helpers;

/**
 * Class DbHelper
 *
 * @package LaraQuiz\Helpers
 */
class DbHelper
{
    /**
     * @param array $columns
     * @param string $tableField
     * @param string $columnName
     * @return array
     */
    public static function getFullColumnNames(
        array $columns,
        string $tableField = 'table',
        string $columnName = 'column'
    ): array {
        $fullColumnsNames = [];
        foreach ($columns as $column) {
            $columnNameAlias = static::getColumnNameAlias($column, $tableField, $columnName);
            if (isset($column['customSql'])) {
                $fullColumnsNames[] = $column['customSql'] . ' AS ' . $columnNameAlias;
            } else {
                $fullColumnsNames[] = $column[$tableField] . '.' . $column[$columnName] . ' AS ' . $columnNameAlias;
            }
        }

        return $fullColumnsNames;
    }

    /**
     * @param array $column
     * @param string $tableField
     * @param string $columnName
     * @return string
     */
    public static function getColumnNameAlias(
        array $column,
        string $tableField = 'table',
        string $columnName = 'column'
    ): string {
        return $column[$tableField] . '_' . $column[$columnName];
    }
}