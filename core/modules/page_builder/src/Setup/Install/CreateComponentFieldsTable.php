<?php

namespace Puzzle\page_builder\Setup\Install;

use Illuminate\Database\Schema\Blueprint;
use Puzzle\Core\Setup\InstallScriptInterface;
use Puzzle\Storage\Database;

class CreateComponentFieldsTable implements InstallScriptInterface
{
    public function install(): void
    {
        Database::schema()->create('component_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('component_id')->references('id')->on('components')->cascadeOnDelete();
            $table->string('field_name');
            $table->string('field_type');
            $table->enum('value_type', [
                'int',
                'varchar',
                'text',
                'json',
                'boolean',
                'blob'
            ]);
            $table->integer('int_value')->nullable()->index();
            $table->string('varchar_value')->nullable()->index();
            $table->text('text_value')->nullable();
            $table->json('json_value')->nullable();
            $table->boolean('bool_value')->nullable()->index();
            $table->binary('blob_value')->nullable();
            $table->integer('weight')->default(0);
            $table->timestamps();
            $table->index(['component_id', 'field_name']);
        });
        Database::statement('ALTER TABLE component_fields MODIFY blob_value LONGBLOB');
    }

    public function getDependencies(): array
    {
        return [
            CreateComponentsTable::class
        ];
    }
}
