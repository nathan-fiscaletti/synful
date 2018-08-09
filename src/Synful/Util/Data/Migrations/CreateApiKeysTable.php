<?php

namespace Synful\Util\Data\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Synful\Util\Data\Migrations\Util\Migration;

class CreateApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema()->create(
            'api_keys', function (Blueprint $table) {
                $table->increments('id');

                $table->string('api_key');
                $table->string('name');
                $table->string('auth');
                $table->boolean('whitelist_only');
                $table->boolean('enabled');
                $table->integer('security_level');
                $table->integer('rate_limit');
                $table->integer('rate_limit_seconds');
                $table->string('allowed_request_handlers');

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema()->drop('api_keys');
    }
}
