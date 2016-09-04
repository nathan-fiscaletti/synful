<?php

namespace Synful\DataManagement\Models;

use Synful\Synful;

/**
 * Class used for handling API Permissions in database
 */
class APIKeyPermissions
{

    /**
     * The API Key associated associated with the permissions
     *
     * @var integer
     */
    public $api_key_id = -1;

    /**
     * Put data permission
     *
     * @var integer
     */
    public $put_data = 0;

    /**
     * Get data permission
     *
     * @var integer
     */
    public $get_data = 0;

    /**
     * Modify data permission
     *
     * @var integer
     */
    public $mod_data = 0;


    /**
     * Create a new instance of APIPermissions
     *
     * @param integer $id
     * @param integer $put_data
     * @param integer $get_data
     * @param integer $mod_data
     */
    public function __construct($id, $put_data = 0, $get_data = 0, $mod_data = 0)
    {
        $this->api_key_id = $id;

        $res = Synful::$sql->executeSql(
            'SELECT * FROM `api_perms` WHERE `api_key_id` = ?',
            [
             's',
             $this->api_key_id,
            ],
            true
        );

        if ($res->num_rows > 0) {
            $res            = mysqli_fetch_assoc($res);
            $this->put_data = $res['put_data'];
            $this->get_data = $res['get_data'];
            $this->mod_data = $res['mod_data'];
        } else {
            $this->put_data = $put_data;
            $this->get_data = $get_data;
            $this->mod_data = $mod_data;
            $this->save();
        }
    }


    /**
     * Save changes made to the permissions
     */
    public function save()
    {
        Synful::$sql->executeSql(
            'INSERT INTO `api_perms` (`api_key_id`, `put_data`, `get_data`,'.
            ' `mod_data`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE '.
            '`put_data` = ?, `get_data` = ?, `mod_data` = ?',
            [
             'iiiiiii',
             (int) $this->api_key_id,
             (int) $this->put_data,
             (int) $this->get_data,
             (int) $this->mod_data,
             (int) $this->put_data,
             (int) $this->get_data,
             (int) $this->mod_data,
            ]
        );
    }


    /**
     * Delete the permissions
     */
    public function delete()
    {
        Synful::$sql->executeSql(
            'DELETE FROM `api_perms` WHERE `api_key_id` = ?',
            [
             's',
             (int) $this->api_key_id,
            ]
        );
    }
}
