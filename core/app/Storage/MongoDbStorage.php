<?php

namespace Puzzle\Storage;

use MongoDB\Client;
use MongoDB\Collection;
use Puzzle\Model\Model;

class MongoDbStorage implements StorageInterface
{
    private Collection $collection;

    public function __construct(Client $client, string $database, string $collection)
    {
        $this->collection = $client->selectCollection($database, $collection);
    }

    public function save(Model $model): bool
    {
        $data = $model->toArray();

        if (isset($data['id'])) {
            $this->collection->replaceOne(['_id' => $data['id']], $data, ['upsert' => true]);
        } else {
            $result = $this->collection->insertOne($data);
            $model->setAttribute('id', $result->getInsertedId());
        }

        return true;
    }
}
