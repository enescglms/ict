<?php

use Illuminate\Support\Facades\DB;

class CustomDatabaseQuery
{
    function getProductsUsedForEachOrderStatus()
    {
        $results = DB::select('
            SELECT
                os.status AS order_status,
                COUNT(op.product_id) AS product_count
            FROM
                orders o
            JOIN
                order_statuses os ON o.status_id = os.id
            JOIN
                order_products op ON o.id = op.order_id
            GROUP BY
                os.status
        ');

        return $results;
    }

    function mostUsedProducts()
    {
        $results = DB::select('
            SELECT
                p.id,
                p.name,
                COUNT(op.product_id) AS order_count
            FROM
                products p
            LEFT JOIN
                order_products op ON p.id = op.product_id
            JOIN
                orders o ON op.order_id = o.id
            WHERE
                p.stock_status = FALSE
                AND o.order_date >= DATE_TRUNC(\'month\', CURRENT_DATE) - INTERVAL \'1 year\'
                AND o.order_date >= DATE_TRUNC(\'month\', CURRENT_DATE) - INTERVAL \'1 month\'
            GROUP BY
                p.id, p.name
            ORDER BY
                order_count DESC
            LIMIT 5;
        ');

        return $results;
    }
}
