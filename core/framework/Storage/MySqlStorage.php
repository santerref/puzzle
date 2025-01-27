<?php

namespace Puzzle\Storage;

use Puzzle\Entity\Model;

use function Symfony\Component\Clock\now;

class MySqlStorage implements StorageInterface
{
    public function save(Model $model): bool
    {
        $data = $model->toArray();
        $now = now();
        $data['updated_at'] = $now;
        if (!$model->has('id')) {
            $data['created_at'] = $now;
            Database::table($model->tableName())->insert($data);
        } else {
            Database::table($model->tableName())->where('id', $model->getAttribute('id'))
                ->update($data);
        }

        return true;
    }
}
