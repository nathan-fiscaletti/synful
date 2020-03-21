<?php

namespace Synful\Data\Migrations\Util;

use Illuminate\Database\Schema\Builder;
use Synful\Data\Database;

abstract class Migration
{
    /**
     * The database connection to use.
     *
     * @var string
     */
    protected $connection = 'synful';

    /**
     * Run the migrations.
     *
     * @return void
     */
    abstract public function up();

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    abstract public function down();

    /**
     * Retrieve the Schema object fot this Migration.
     *
     * @return Builder
     */
    public function schema()
    {
        return Database::capsule()
            ->getConnection($this->connection)
            ->getSchemaBuilder();
    }
}
