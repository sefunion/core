<?php


declare(strict_types=1);


namespace Easy;

use Hyperf\Database\Commands\Ast\ModelUpdateVisitor;
use Easy\Annotation\DependProxy;

/**
 * class EasyModelVisitor.
 */
#[DependProxy(values: [ModelUpdateVisitor::class])]
class EasyModelVisitor extends ModelUpdateVisitor
{
    protected function formatDatabaseType(string $type): ?string
    {
        return match ($type) {
            'tinyint', 'smallint', 'mediumint', 'int', 'bigint' => 'integer',
            'decimal' => 'decimal:2',
            'float', 'double', 'real' => 'float',
            'bool', 'boolean' => 'boolean',
            'json' => 'array',
            default => null,
        };
    }

    protected function formatPropertyType(string $type, ?string $cast): ?string
    {
        if (! isset($cast)) {
            $cast = $this->formatDatabaseType($type) ?? 'string';
        }

        switch ($cast) {
            case 'integer':
                return 'int';
            case 'date':
            case 'datetime':
                return '\Carbon\Carbon';
            case 'json':
                return 'array';
        }

        if (Str::startsWith($cast, 'decimal')) {
            // 如果 cast 为 decimal，则 @property 改为 string
            return 'string';
        }

        return $cast;
    }
}
