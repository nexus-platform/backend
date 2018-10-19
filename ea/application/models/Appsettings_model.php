<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

/**
 * Settings Model
 *
 * @package Models
 */
class Appsettings_model extends CI_Model {

    /**
     * Returns all the system settings at once.
     *
     * @return array Array of all the system settings stored in the 'ea_settings' table.
     */
    public function get_settings() {
        $this->db
                ->select('app_settings.*')
                ->from('app_settings');
        return $this->db->get()->result_array();
    }

}
