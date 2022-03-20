<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table;

    /**
     * Primary key
     *
     * @var mixed
     * @access protected
     */
    protected $primary_key;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->driver('cache', array(
            'adapter' => 'file',
            'backup' => 'file'
        ));

        $this->load->database();

        $this->db->simple_query("SET SQL_MODE = 'NO_ZERO_DATE'");

        $this->_set_table();
    }

    /**
     * Set table
     *
     * @access private
     * @return void
     */
    private function _set_table()
    {
        if ($this->table == null)
        {
            $class = preg_replace('/(_model|_Model)?$/', '', get_class($this));
            $class = str_replace('model_', '', $class);
            $class = str_replace('Model_', '', $class);
            $this->table = strtolower($class);
        }

        if ($this->primary_key == null)
        {
            $this->primary_key = $this->table.'_id';
        }
    }

    /**
     * Set where
     *
     * @access private
     * @param array $params
     * @return void
     */
    private function _set_where($params = array())
    {
        if (count($params) == 1)
        {
            $this->db->where($params[0]);
        }
        else
        {
            if (is_numeric($params[1]))
            {
                $params[1] = (int)$params[1];
            }

            $this->db->where($params[0], $params[1]);
        }
    }

    /**
     * Trigger
     *
     * @access private
     * @param string $event
     * @param mixed $id
     * @param mixed $data
     * @return void
     */
    private function _trigger($event, $id = null, $data = false)
    {
        if ( ! isset($this->$event) || ! is_array($this->$event))
        {
            return $data;
        }

        foreach ($this->$event as $method)
        {
            call_user_func_array(array($this, $method), array($id, $data));
        }
    }

    /**
     * Get date time for now
     *
     * @access protected
     * @return string
     */
    protected function now()
    {
        return date('Y-m-d H:i:s', time());
    }

    /**
     * Set data
     *
     * @access protected
     * @param array $data
     * @param string $table
     * @return array
     */
    protected function set_data($data = array(), $table = null)
    {
        $table_name = is_null($table) ? $this->table : $table;

        $set_data = array();

        if ($this->db->field_exists('date_modified', $table_name))
        {
            $set_data['date_modified'] = $this->now();
        }

        foreach ($this->db->field_data($table_name) as $field)
        {
            if (isset($data[$field->name]))
            {
                switch($field->type)
                {
                    case 'int':
                        $set_data[$field->name] = (int)$data[$field->name];
                    break;

                    case 'tinyint':
                        $set_data[$field->name] = (int)$data[$field->name];
                    break;

                    case 'decimal':
                        $set_data[$field->name] = (float)$data[$field->name];
                    break;

                    case 'date':
                        $set_data[$field->name] = date('Y-m-d', strtotime($data[$field->name]));
                    break;

                    case 'datetime':
                        $set_data[$field->name] = date('Y-m-d H:i:s', strtotime($data[$field->name]));
                    break;

                    default:
                        $set_data[$field->name] = $data[$field->name];
                    break;
                }
            }
        }

        return $set_data;
    }

    /**
     * Get a single record
     *
     * @access public
     * @param mixed $primary_value
     * @return array
     */
    public function get($primary_value = null)
    {
        $this->_set_where(array($this->primary_key, $primary_value));

        return $this->db->get($this->table)->row_array();
    }

    /**
     * Get a single record by parameter
     *
     * @access public
     * @return array
     */
    public function get_by()
    {
        $where = func_get_args();

        if ( ! $where)
        {
            return array();
        }

        $this->_set_where($where);

        return $this->db
        ->get($this->table)
        ->row_array();
    }

    /**
     * Get multiple records by parameters
     *
     * @access public
     * @return array
     */
    public function get_all_by()
    {
        $where = func_get_args();

        if ( ! $where)
        {
            return array();
        }

        $this->_set_where($where);

        return $this->get_all();
    }

    /**
     * Get multiple records
     *
     * @access public
     * @return array
     */
    public function get_all()
    {
        if ($this->db->field_exists('date_deleted', $this->table)){
            $this->db->where('date_deleted', NULL);
        }
        return $this->db
        ->get($this->table)
        ->result_array();
    }

    /**
     * Insert new record
     *
     * @access public
     * @param array $data
     * @return void
     */
    public function insert($data = array())
    {
        if ($this->db->field_exists('date_added', $this->table))
        {
            if (!isset($data['date_added']))
            {
                $data['date_added'] = $this->now();
            }
        }

        $this->db->set($this->set_data($data));
        $this->db->insert($this->table);

        return $this->db->insert_id();
    }

    /**
     * Insert batch
     *
     * @access public
     * @param array $data
     * @return bool
     */
    public function insert_batch($data = array())
    {
        $batch_data = array();

        foreach ($data as $value) {
            $batch_data[] = $this->set_data($value);
        }

        if ($batch_data) {
            $this->db->insert_batch($this->table, $batch_data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update existing record
     *
     * @access public
     * @param mixed $primary_value
     * @param array $data
     * @return void
     */
    public function update($primary_value = null, $data = array())
    {
        if (is_array($primary_value))
        {
            $this->db->where_in($this->primary_key, (array)$primary_value);
        }
        else
        {
            $this->db->where($this->primary_key, $primary_value);
        }

        $this->db->set($this->set_data($data));
        $this->db->update($this->table);

        return $this->db->affected_rows();
    }

    /**
     * Delete permanently
     *
     * @access public
     * @param mixed $primary_value
     * @return void
     */
    public function delete($primary_value = null)
    {
        if (is_array($primary_value))
        {
            $this->db->where_in($this->primary_key, (array)$primary_value);
        }
        else
        {
            $this->db->where($this->primary_key, $primary_value);
        }

        $this->db->delete($this->table);

        return $this->db->affected_rows();
    }

    /**
     * Delete permanently by condition
     *
     * @access public
     * @return void
     */
    public function delete_by()
    {
        $where = func_get_args();

        if ( ! $where)
        {
            return false;
        }

        $this->_set_where($where);

        $this->db->delete($this->table);

        return $this->db->affected_rows();
    }

    /**
     * Temporary delete
     *
     * @access public
     * @param mixed $primary_value
     * @return void
     */
    public function soft_delete($primary_value = null)
    {
        if ($this->db->field_exists('date_deleted', $this->table))
        {
            if (is_array($primary_value))
            {
                $this->db->where_in($this->primary_key, (array)$primary_value);
            }
            else
            {
                $this->db->where($this->primary_key, $primary_value);
            }

            $this->db->set($this->set_data(array('date_deleted', date('Y-m-d H:i:s'))));

            print_r($this->set_data(array('date_deleted', date('Y-m-d H:i:s'))));
            $this->db->update($this->table);

            return $this->db->affected_rows();
        }

        return false;
    }

    /**
     * Temporary delete by condition
     *
     * @access public
     * @return void
     */
    public function soft_delete_by()
    {
        if ($this->db->field_exists('deleted', $this->table))
        {
            $where = func_get_args();

            if ( ! $where)
            {
                return false;
            }

            $this->_set_where($where);

            $this->db->set($this->set_data(array('deleted', 1)));
            $this->db->update($this->table);

            return $this->db->affected_rows();
        }

        return false;
    }

    /**
     * Restore temporary deleted record
     *
     * @access public
     * @return void
     */
    public function undelete()
    {
        if ($this->db->field_exists('deleted', $this->table))
        {
            if (is_array($primary_value))
            {
                $this->db->where_in($this->primary_key, (array)$primary_value);
            }
            else
            {
                $this->db->where($this->primary_key, $primary_value);
            }

            $this->db->set($this->set_data(array('deleted', 0)));
            $this->db->update($this->table);

            return $this->db->affected_rows();
        }

        return false;
    }

    /**
     * Restore temporary deleted record by condition
     *
     * @access public
     * @return void
     */
    public function undelete_by()
    {
        if ($this->db->field_exists('deleted', $this->table))
        {
            $where = func_get_args();

            if ( ! $where)
            {
                return false;
            }

            $this->_set_where($where);

            $this->db->set($this->set_data(array('deleted', 0)));
            $this->db->update($this->table);

            return $this->db->affected_rows();
        }

        return false;
    }

    /**
     * Is existsing record?
     *
     * @access public
     * @param mixed $value
     * @param string $field
     * @return bool
     */
    public function is_exists($value, $field = false)
    {
        $query = $this->db->select($this->primary_key);

        if ($field === false)
        {
            $query = $this->db->where($this->primary_key, $value);
        }
        else
        {
            if ($this->db->field_exists($field, $this->table))
            {
                $query = $this->db->where($field, $value);
            }
            else
            {
                return false;
            }
        }

        return (bool) $query->count_all_results($this->table);
    }


    /**
     * Is unique record?
     *
     * @access public
     * @param string $field
     * @param mixed $value
     * @param mixed $primary_value
     * @return bool
     */
    public function is_unique($field = '', $value = '', $primary_value = null)
    {
        if ( ! $this->db->field_exists($field, $this->table))
        {
            return true;
        }

        if ($value == '')
        {
            return true;
        }

        $this->db->select($this->primary_key);

        if ($primary_value)
        {
            $this->db->where($this->primary_key.' !=', $primary_value);
        }

        return (bool)$this->db
        ->where($field, strtoupper($value))
        ->count_all_results($this->table);
    }

    /**
     * List fields
     *
     * @access public
     * @return array
     */
    public function list_fields()
    {
        return $this->db->list_fields($this->table);
    }
}