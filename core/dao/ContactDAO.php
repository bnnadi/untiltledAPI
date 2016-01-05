<?php
/**
 * Created by PhpStorm.
 * User: BNnadi
 * Date: 1/5/2016
 * Time: 9:19 AM
 */

namespace cms\core\dao;


class ContactDAO extends DAO
{
    function __construct()
    {
        parent::__construct();
        $this->table = 'contacts';
        $this->id_column = 'contact_id';
        $this->model = '\bisikecms\core\model\Contact';
        $this->table_fields = array('contact_contact_type_id', 'contact_owner_user_id', 'contact_title', 'contact_first_name',
            'contact_last_name', 'contact_email','contact_company_name', 'contact_created_date', 'contact_modified_date',
            'contact_status_id', 'contact_visible');
        $this->visible_field = 'contact_visible';
        $this->addHasOne('statuses', 'status_id', 'contact_status_id');
    }
}