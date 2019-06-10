<?php
namespace App\Elasticsearch;

class QueryHelper
{

    /**
     * Get match query
     *
     * @param string $field
     * @param mixed $value
     * @param string $type
     *
     * @return array
     */
    public static function matchQuery($field, $value, $type = 'query')
    {
        return [
            'match' => [
                $field => [
                    $type => $value
                ]
            ]
        ];
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return array
     */
    public static function termQuery($field, $value)
    {
        return [
            'term' => [
                $field => $value
            ]
        ];
    }

    /**
     * Get range query
     *
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     *
     * @return array
     */
    public static function rangeQuery($field, $from = null, $to = null)
    {
        $query = [
            'range' => [
                $field => []
            ]
        ];
        if ($from) {
            $query['range'][$field]['gte'] = $from;
        }
        if ($to) {
            $query['range'][$field]['lte'] = $to;
        }
        return $query;
    }

    /**
     * Get match query
     *
     * @param array $fields
     *
     * @return array
     */
    public static function matchAllQuery($fields)
    {
        $matches = [];
        foreach ($fields as $field => $value) {
            $matches[] = self::matchQuery($field, $value);
        }
        return [
            'match_all' => [
                $matches
            ]
        ];
    }
}
