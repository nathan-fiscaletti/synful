<?php

namespace Synful\Data\Models;

use Illuminate\Database\Eloquent\Model;

class FirewallEntry extends Model
{
    /**
     * The database connection to use for this Model.
     *
     * @var string
     */
    protected $connection = 'synful';

    /**
     * The table to associate this Model with.
     *
     * @var string
     */
    protected $table = 'ip_firewall';

    /**
     * The fillable properties for this Model.
     *
     * @var array
     */
    protected $fillable = [
        'ip',
        'block',
        'api_key_id',
    ];

    /**
     * The properties to hide for this Model.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];
}
