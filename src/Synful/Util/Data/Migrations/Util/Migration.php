<?php

namespace Synful\Util\Data\Migrations\Util;

use Synful\Util\Data\Database;

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
    public abstract function up();

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public abstract function down();

    /**
     * Retrieve the Schema object fot this Migration.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function schema()
    {
        return Database::capsule()
            ->getConnection($this->connection)
            ->getSchemaBuilder();
    }
}