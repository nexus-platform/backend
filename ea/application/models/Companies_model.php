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
 * Companies Model
 *
 * @package Models
 */
class Companies_model extends CI_Model {

    /**
     * Get the record id of a particular company.
     *
     * @param string $slug The selected role slug. Slugs are defined in the "application/config/constants.php" file.
     *
     * @return CI_Model Returns the database row.
     */
    public function find($slug) {
        return $this->db->get_where('assessment_center', ['url' => $slug])->row();
    }

}
