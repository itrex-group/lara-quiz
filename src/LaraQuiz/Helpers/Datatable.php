<?php
declare(strict_types=1);

namespace LaraQuiz\Helpers;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Datatable
 *
 * @package LaraQuiz\Helpers
 */
class Datatable
{
    /**
     * @param array $columns
     * @return array
     */
    public static function getFullColumnNames(array $columns): array
    {
        return DbHelper::getFullColumnNames($columns, 'table', 'db');
    }

    /**
     * @param array $column
     * @return string
     */
    public static function getColumnNameAlias(array $column): string
    {
        return DbHelper::getColumnNameAlias($column, 'table', 'db');
    }

    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array.
     * @param array $data Data from the SQL get.
     * @return array Formatted data in a row based format.
     */
    public static function getOutput(array $columns, array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $item = [];

            foreach ($columns as $column) {
                $item[$column['dt']] = static::handleFormatter($column, $row);
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param array $column Column information.
     * @param array|object $row Data from DB.
     * @return mixed
     */
    public static function handleFormatter(array $column, $row)
    {
        $columnName = static::getColumnNameAlias($column);
        // Is there a formatter?
        if (isset($column['formatter'])) {
            return $column['formatter']($row[$columnName], $row);
        } else {
            return $row[$columnName];
        }
    }

    /**
     * @param array $request
     * @param array $columns
     * @return array
     */
    public static function filterCustom(array $request, array $columns): array
    {
        $globalSearch = [];
        $columnSearch = [];

        $dtColumns = array_pluck($columns, 'dt');

        if (isset($request['search']) && trim($request['search']['value'], '') !== '') {
            $str = trim($request['search']['value']);

            foreach ($request['columns'] as $requestColumn) {
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if (isset($column['customSql'])) {
                    $columnString = $column['where_name'];
                } else {
                    $columnString = $column['table'] . '.' . $column['db'];
                }

                if ($requestColumn['searchable'] === 'true') {
                    $globalSearch[] = [
                        'column' => $columnString,
                        'value' => $str,
                    ];
                }
            }
        }

        // Individual column filtering
        if (isset($request['columns'])) {
            foreach ($request['columns'] as $requestColumn) {
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['search']['value'] === null) {
                    $requestColumn['search']['value'] = '';
                }

                $str = trim($requestColumn['search']['value']);

                if ($requestColumn['searchable'] === 'true' && $str !== '') {
                    if (isset($column['customSql'])) {
                        $columnString = $column['where_name'];
                    } else {
                        $columnString = $column['table'] . '.' . $column['db'];
                    }
                    $columnSearch[] = [
                        'column' => $columnString,
                        'value' => $str,
                    ];
                }
            }
        }

        return [
            'globalSearch' => $globalSearch,
            'columnSearch' => $columnSearch,
        ];
    }

    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param array $request Data sent to server by DataTables.
     * @param array $columns Column information array.
     * @return array
     */
    public static function orderCustom(array $request, array $columns): array
    {
        $orderBy = [];

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = [];
            $dtColumns = array_column($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = (int)$request['order'][$i]['column'];
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] === 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    if (isset($column['customSql'])) {
                        $columnString = static::getColumnNameAlias($column);
                    } else {
                        $columnString = $column['table'] . '.' . $column['db'];
                    }
                    $orderBy[] = [
                        'column' => $columnString,
                        'value' => $dir,
                    ];
                }
            }
        }

        return $orderBy;
    }

    /**
     * @param array $columns
     * @return array
     */
    public static function columnsInterpreter(array $columns): array
    {
        foreach ($columns as $key => $column) {
            $columns[$key]['dt'] = $key;
        }

        return $columns;
    }

    /**
     * Filters for Query builder
     *
     * @param Builder $query
     * @param array $columns
     * @param array $request
     * @return void
     */
    public static function filterQuery(Builder $query, array $columns, array $request): void
    {
        // add WHERE statement to the query
        $conditions = Datatable::filterCustom($request, $columns);

        if (!empty($conditions['globalSearch'])) {
            $globalSearch = $conditions['globalSearch'];
            $query->where(function ($q) use ($globalSearch) {
                foreach ($globalSearch as $params) {
                    $q->orWhere($params['column'], 'like', '%' . $params['value'] . '%');
                }
            });
        }

        if (!empty($conditions['columnSearch'])) {
            $columnSearch = $conditions['columnSearch'];
            $query->where(function ($q) use ($columnSearch) {
                foreach ($columnSearch as $params) {
                    $q->where($params['column'], 'like', '%' . $params['value'] . '%');
                }
            });
        }
        // end add WHERE statement to the query
    }

    /**
     * Sorting for Query builder
     *
     * @param Builder $query
     * @param array $columns
     * @param array $request
     * @return void
     */
    public static function sortQuery(Builder $query, array $columns, array $request): void
    {
        if (isset($request['start']) && $request['length'] !== -1) {
            $query->limit((int)$request['length']);
            $query->offset((int)$request['start']);
        }

        $orders = Datatable::orderCustom($request, $columns);
        foreach ($orders as $order) {
            $query->orderBy($order['column'], $order['value']);
        }
    }

    /**
     * Received data to create response for DataTables
     *
     * @param array $request
     * @param int $recordsTotal
     * @param int $recordsFiltered
     * @param array $columns
     * @param Builder $query
     * @return array
     */
    public static function prepareResponse(
        array $request,
        int $recordsTotal,
        int $recordsFiltered,
        array $columns,
        Builder $query
    ): array {
        return [
            'draw' => isset($request['draw']) ? (int)$request['draw'] : 0,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => Datatable::getOutput($columns, $query->get()->all()),
        ];
    }
}