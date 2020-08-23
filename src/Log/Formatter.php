<?php

namespace Olivier\Mezzio\Log;

use Monolog\Formatter\JsonFormatter;

class Formatter extends JsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {
        $record['level'] = $record['level_name'];
        $record['tags'] = $record['context']['tags'] ?? null;

        unset($record['level_name']);

        return parent::format($record);
    }
}
