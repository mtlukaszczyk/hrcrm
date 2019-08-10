<?php

namespace App\Helper;

trait DataTables {

    public static function prepareColumnNumbers() {

        $columnNumbers = [];
        $i = 0;
        foreach (self::$dataTableOptions['columns'] as $option) {
            $columnNumbers[$option['id']] = $i++;
        }

        return $columnNumbers;
    }

    private static function escapeLike(string $value, string $char = '\\'): string {
        return str_replace(
                [$char, '%', '_'], [$char . $char, $char . '%', $char . '_'], $value
        );
    }

    public static function getData($draw, $columns, $order, $start, $length, $search, $additionalFilters = [], $additionalData = []) {

        if (self::$dataTableOptions == []) {
            self::prepareDataTableOptions();
        }

        $additionalData['columnNumbers'] = self::prepareColumnNumbers();

        $columnsStructure = isset(self::$dataTableOptions['columns']) ? self::$dataTableOptions['columns'] : [];
        $wheres = isset(self::$dataTableOptions['wheres']) ? self::$dataTableOptions['wheres'] : [];

        $columnsNamesPrepared = [];

        $i = 0;
        foreach ($columnsStructure as $column) {
            $columnsNamesPrepared[] = ($column['name'] . ' as ' . $i++);
        }

        $additionalFilterOptions = isset(self::$dataTableOptions['additionalFilterOptions']) ? self::$dataTableOptions['additionalFilterOptions'] : [];
        $joins = isset(self::$dataTableOptions['joins']) ? self::$dataTableOptions['joins'] : [];

        $dataTotalCount = self::when(count($joins) > 0, function($query) use ($joins) {
                    foreach ($joins as $join) {
                        $query = $query->{$join['type']}($join['table'], $join['leftField'], $join['operator'], $join['rightField']);
                    }

                    return $query;
                })->when($wheres !== [], function($query) use ($wheres) {
                    foreach ($wheres as $where) {
                        $query = $query->whereRaw($where);
                    }
                    return $query;
                })->count();

        $dataFilteredCount = self::when(count($joins) > 0, function($query) use ($joins) {
                    foreach ($joins as $join) {
                        $query = $query->{$join['type']}($join['table'], $join['leftField'], $join['operator'], $join['rightField']);
                    }

                    return $query;
                })->where(function($query) use ($columnsStructure, $columns, $search) {
                    foreach ($columnsStructure as $key => $columnFromDB) {
                        if ($columns[$key]['searchable'] == 'true' && $search['value'] !== '') {
                            $query = $query->orWhere($columnFromDB['name'], 'like', '%' . $search['value'] . '%');
                        }
                    }

                    return $query;
                })
                ->when(count($additionalFilters) > 0, function($query) use ($additionalFilters, $additionalFilterOptions) {
                    $query->where(function($query) use ($additionalFilters, $additionalFilterOptions) {

                        foreach ($additionalFilters as $filter => $value) {
                            if ($value !== '') {
                                if (isset($additionalFilterOptions[$filter])) {
                                    if ($additionalFilterOptions[$filter]['operator'] == 'in') {
                                        $query = $query->whereIn($additionalFilterOptions[$filter]['field'], $value);
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'not-in') {
                                        $query = $query->whereNotIn($additionalFilterOptions[$filter]['field'], $value);
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'like') {
                                        $query = $query->where($additionalFilterOptions[$filter]['field'], 'like', '%' . self::escapeLike($value) . '%');
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'not-like') {
                                        $query = $query->whereNot($additionalFilterOptions[$filter]['field'], 'like', '%' . self::escapeLike($value) . '%');
                                    } else {
                                        $query = $query->where($additionalFilterOptions[$filter]['field'], $additionalFilterOptions[$filter]['operator'], $value);
                                    }
                                } else {
                                    $query = $query->where($filter, '=', $value);
                                }
                            }
                        }

                        return $query;
                    });
                })
                ->when($wheres !== [], function($query) use ($wheres) {
                    foreach ($wheres as $where) {
                        $query = $query->whereRaw($where);
                    }
                    return $query;
                })
                ->count();

        $data = self::when(count($joins) > 0, function($query) use ($joins) {
                    foreach ($joins as $join) {
                        $query = $query->{$join['type']}($join['table'], $join['leftField'], $join['operator'], $join['rightField']);
                    }

                    return $query;
                })
                ->when($length > 0, function($query) use ($length, $start) {
                    return $query->limit($length)
                            ->offset($start);
                })
                ->where(function($query) use ($columnsStructure, $columns, $search) {
                    foreach ($columnsStructure as $key => $columnFromDB) {
                        if ($columns[$key]['searchable'] == 'true' && $search['value'] !== '') {
                            $query = $query->orWhere($columnFromDB['name'], 'like', '%' . self::escapeLike($search['value']) . '%');
                        }
                    }

                    return $query;
                })
                ->when(count($additionalFilters) > 0, function($query) use ($additionalFilters, $additionalFilterOptions) {
                    $query->where(function($query) use ($additionalFilters, $additionalFilterOptions) {
                        foreach ($additionalFilters as $filter => $value) {
                            if ($value !== '') {
                                if (isset($additionalFilterOptions[$filter])) {
                                    if ($additionalFilterOptions[$filter]['operator'] == 'in') {
                                        $query = $query->whereIn($additionalFilterOptions[$filter]['field'], $value);
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'not-in') {
                                        $query = $query->whereNotIn($additionalFilterOptions[$filter]['field'], $value);
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'like') {
                                        $query = $query->where($additionalFilterOptions[$filter]['field'], 'like', '%' . self::escapeLike($value) . '%');
                                    } else if ($additionalFilterOptions[$filter]['operator'] == 'not-like') {
                                        $query = $query->whereNot($additionalFilterOptions[$filter]['field'], 'like', '%' . self::escapeLike($value) . '%');
                                    } else {
                                        $query = $query->where($additionalFilterOptions[$filter]['field'], $additionalFilterOptions[$filter]['operator'], $value);
                                    }
                                } else {
                                    $query = $query->where($filter, '=', $value);
                                }
                            }
                        }

                        return $query;
                    });
                })
                ->when($columns[$order[0]['column']]['orderable'] == 'true', function($query) use ($order) {
                    return $query->orderBy($order[0]['column'], $order[0]['dir']);
                })
                ->when($wheres !== [], function($query) use ($wheres) {
                    foreach ($wheres as $where) {
                        $query = $query->whereRaw($where);
                    }
                    return $query;
                })
                ->get($columnsNamesPrepared)
                ->toArray();

        foreach ($data as &$rowData) {

            $rowDataCopy = $rowData;

            foreach ($rowData as $cellID => &$cellData) {
                if (isset($columnsStructure[$cellID]['filter'])) {
                    $cellData = $columnsStructure[$cellID]['filter']($cellData, $rowDataCopy, $additionalData);
                }
            }
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $dataTotalCount,
            'recordsFiltered' => $dataFilteredCount,
            'data' => $data
        ];
    }

}
