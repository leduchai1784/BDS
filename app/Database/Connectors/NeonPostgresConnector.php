<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);

        if (isset($config['options'])) {
            $options = $config['options'];
            if (is_array($options) && isset($options['options'])) {
                $options = $options['options'];
            }
            if (is_string($options) && !empty($options)) {
                $dsn .= ";options='{$options}'";
            }
        }

        return $dsn;
    }
}
