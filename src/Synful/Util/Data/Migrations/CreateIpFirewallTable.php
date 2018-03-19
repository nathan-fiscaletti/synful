<?php

namespace Synful\Util\Data\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Synful\Util\Data\Migrations\Util\Migration;

class CreateIpFirewallTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema()->create(
            'ip_firewall', function (Blueprint $table) {
                $table->increments('id');

                $table->string('ip');
                $table->boolean('block');
                $table->integer('api_key_id');

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema()->drop('ip_firewall');
    }
}
