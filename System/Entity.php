<?php

namespace Floxim\Floxim\System;

use Floxim\Floxim\Template;

abstract class Entity implements \ArrayAccess
{

    // reference to the class object fx_data_
    //protected $finder;
    // field values
    protected $data = array();
    // the set of fields that have changed
    protected $modified = array();
    protected $modified_data = array();

    protected $validate_errors = array();

    protected $_form = null;
    
    // Extra data from forms etc.
    protected $payload = array();

    protected function getFinder()
    {
        return fx::data($this->getType());
    }
    
    public function getPayload($key = null) 
    {
        return is_null($key) ? $this->payload : (isset($this->payload[$key]) ? $this->payload[$key] : null);
    }
    
    public function setPayload($key, $value)
    {
        $this->payload[$key] = $value;
        return $this;
    }

    protected static $_field_map = array();

    // virtual field types
    const VIRTUAL_RELATION = 0;
    const VIRTUAL_MULTILANG = 1;
    
    protected $is_loaded = false;

    public function __construct($input = array())
    {
        static $ec = 0;
        $ec++;

        $this->loadFieldMap();

        if (isset($input['data']) && $input['data']) {
            $data = $input['data'];
            if (isset($data['id'])) {
                $this->is_loaded = false;
            }
            foreach ($data as $k => $v) {
                $this[$k] = $v;
            }
        }
        $this->is_loaded = true;
    }

    protected function loadFieldMap()
    {
        // cache relations & ml on first use
        // to increase offsetExists() speed (for isset($n[$val]) in templates)
        $c_type = $this->getType();
        if (!isset(self::$_field_map[$c_type])) {
            $finder = $this->getFinder();
            self::$_field_map[$c_type] = array();
            foreach ($finder->relations() as $rel_name => $rel) {
                self::$_field_map[$c_type][$rel_name] = array(self::VIRTUAL_RELATION, $rel);
            }
            foreach ($finder->getMultiLangFields() as $f) {
                self::$_field_map[$c_type][$f] = array(self::VIRTUAL_MULTILANG);
            }
        }
    }

    public function __wakeup()
    {
        $this->loadFieldMap();
    }

    public function save()
    {
        $this->beforeSave();
        $pk = $this->getPk();
        // update
        if (isset($this->data[$pk]) && $this->data[$pk]) {
            $this->beforeUpdate();
            if ($this->validate() === false) {
                $this->throwInvalid();
                return false;
            }
            // updated only fields that have changed
            $data = array();
            foreach ($this->modified as $v) {
                $data[$v] = $this->data[$v];
            }
            $this->getFinder()->update($data, array($pk => $this->data[$pk]));
            $this->saveMultiLinks();
            $this->afterUpdate();
        } // insert
        else {
            $this->beforeInsert();
            if ($this->validate() === false) {
                $this->throwInvalid();
                return false;
            }
            $id = $this->getFinder()->insert($this->data);
            $this->data['id'] = $id;
            $this->saveMultiLinks();
            $this->afterInsert();
        }
        $this->afterSave();

        return $this;
    }

    /**
     * Throw validation exception or append errors to form if it exists
     * @throws fx_entity_validation_exception
     */
    protected function throwInvalid()
    {
        $exception = new Exception\EntityValidation(
            fx::lang("Unable to save entity \"" . $this->getType() . "\"")
        );
        $exception->addErrors($this->validate_errors);
        $form = $this->getBoundForm();
        if ($form) {
            $exception->toForm($form);
        } else {
            throw $exception;
        }
    }

    protected function invalid($message, $field = null)
    {
        $error = array(
            'text' => $message
        );
        if ($field) {
            $error['field'] = $field;
        }
        $this->validate_errors[] = $error;
    }


    /*
     * Saves the fields links is determined in fx_data_content
     */
    protected function saveMultiLinks()
    {

    }

    protected function beforeSave()
    {

    }

    protected function afterSave()
    {
        $finder_class = get_class($this->getFinder());
        $finder_class::dropStoredStaticCache();
    }

    /**
     * Get a property data or an entire set of properties
     * @param strign $prop_name
     * @return mixed
     */
    public function get($prop_name = null)
    {
        if ($prop_name) {
            return $this->offsetGet($prop_name);
        }
        return $this->data;
    }

    public function set($item, $value = '')
    {
        if (is_array($item) || $item instanceof \Traversable) {
            foreach ($item as $k => $v) {
                $this->set($k, $v);
            }
            return $this;
        }
        $this->offsetSet($item, $value);
        return $this;
    }

    public function digSet($path, $value)
    {
        $parts = explode(".", $path, 2);
        if (count($parts) == 1) {
            $this->offsetSet($path, $value);
            return $this;
        }
        $c_value = $this[$parts[0]];
        if (!is_array($c_value)) {
            $c_value = array();
        }
        fx::digSet($c_value, $parts[1], $value);
        $this->offsetSet($parts[0], $c_value);
        return $this;
    }

    public function getId()
    {
        return $this->data[$this->getPk()];
    }

    public function delete()
    {
        $pk = $this->getPk();
        $this->beforeDelete();
        $this->getFinder()->delete($pk, $this->data[$pk]);
        $this->modified_data = $this->data;
        $this->afterDelete();
    }

    public function unchecked()
    {
        return $this->set('checked', 0)->save();
    }

    public function checked()
    {
        return $this->set('checked', 1)->save();
    }

    public function validate()
    {
        return count($this->validate_errors) == 0;
    }

    public function loadFromForm($form, $fields = null)
    {
        $vals = $this->getFromForm($form, $fields);
        $this->set($vals);
        $this->bindForm($form);
        return $this;
    }

    public function bindForm(\Floxim\Form\Form $form)
    {
        $this->_form = $form;
    }
    
    public function getBoundForm()
    {
        return isset($this->_form) ? $this->_form : null;
    }


    protected function getFromForm($form, $fields = null)
    {
        if (is_array($fields)) {
            $vals = array();
            foreach ($fields as $f) {
                $vals[] = $form->$f;
            }
        } else {
            $vals = $form->getValues();
        }
        return $vals;
    }

    public function validateWithForm($form = null, $fields = null)
    {
        if ($form === null) {
            $form = $this->getBoundForm();
        } elseif ($form) {
            $this->bindForm($form);
        }
        if (!$form) {
            throw new \Exception('No form to validate with');
        }
        $this->loadFromForm($form, $fields);
        if (!$this->validate()) {
            $this->throwInvalid();
            return false;
        }
        return true;
    }

    public function getValidateErrors()
    {
        return $this->validate_errors;
    }

    protected function getPk()
    {
        return 'id';
    }

    public function __toString()
    {
        $res = '';
        foreach ($this->data as $k => $v) {
            $res .= "$k = $v " . PHP_EOL;
        }
        return $res;
    }

    protected function beforeInsert()
    {
        return false;
    }

    protected function afterInsert()
    {
        return false;
    }

    protected function beforeUpdate()
    {
        return false;
    }

    protected function afterUpdate()
    {
        return false;
    }

    protected function beforeDelete()
    {
        return false;
    }

    protected function afterDelete()
    {
        $finder_class = get_class($this->getFinder());
        $finder_class::dropStoredStaticCache();
        return false;
    }

    protected static function isTemplateVar($var)
    {
        return mb_substr($var, 0, 1) === '%';
    }
    
    protected $allowTemplateOverride = true;

    /* Array access */
    public function offsetGet($offset)
    {

        // handle template-content vars like $item['%description']
        if (self::isTemplateVar($offset)) {
            $offset = mb_substr($offset, 1);
            if (!isset($this[$offset]) || $this->allowTemplateOverride) {
                $template = fx::env()->getCurrentTemplate();
                if ($template && $template instanceof Template\Template) {
                    $template_value = $template->v($offset . "_" . $this['id']);
                    if ($template_value) {
                        return $template_value;
                    }
                }
            }
        }

        $getter = '_get' . fx::util()->underscoreToCamel($offset);
        if (method_exists($this, $getter)) {
            return call_user_func(array($this, $getter));
        }

        if (array_key_exists($offset, $this->data)) {
            return $this->data[$offset];
        }
        if ($offset == 'id') {
            return null;
        }
        
        $event_result = fx::trigger('offsetGet', array('target' => $this, 'offset' => $offset));
        if ($event_result) {
            return $event_result;
        }


        $c_type = $this->getType();
        $c_field = isset(self::$_field_map[$c_type][$offset]) ? self::$_field_map[$c_type][$offset] : null;

        if (!$c_field) {
            return null;
        }

        if ($c_field[0] == self::VIRTUAL_MULTILANG) {
            $lang_offset = $offset . '_' . fx::config('lang.admin');
            if (!empty($this->data[$lang_offset])) {
                return $this->data[$lang_offset];
            }
            return $this->data[$offset . '_en'];
        }

        /**
         * For example, for $post['tags'], where tags - field-multiphase
         * If connected not loaded, ask finder download
         */

        $finder = $this->getFinder();
        $finder->addRelated($offset, new Collection(array($this)));
        if (!isset($this->data[$offset])) {
            return null;
        }
        //$this->modified_data[$offset] = clone $this->data[$offset];
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        // put modified | modified_data only if there was a key
        // so when you first fill fields-ties they will not be marked as updated
        $offset_exists = array_key_exists($offset, $this->data);

        if (!$offset_exists) {
            $c_type = $this->getType();
            
            if (isset(self::$_field_map[$c_type]) && isset(self::$_field_map[$c_type][$offset])) {
                $c_field = self::$_field_map[$c_type][$offset];
                switch ($c_field[0]) {
                    case self::VIRTUAL_MULTILANG:
                        $offset = $offset . '_' . fx::config('lang.admin');
                        break;
                    case self::VIRTUAL_RELATION:
                        /**
                         * I.e. when the whole parent is set instead of parent_id:
                         * $item['parent'] = $parent_obj;
                         * we add parent_id right now
                         */
                        if ($c_field[1][0] === Finder::BELONGS_TO && $value instanceof Entity) {
                            $c_rel_field = $c_field[1][2];
                            $value_id = $value['id'];
                            if ($c_rel_field && $value_id) {
                                $this[$c_rel_field] = $value_id;
                            }
                        }
                        break;
                }
            }
        }

        if (!$this->is_loaded) {
            $this->data[$offset] = $value;
            return;
        }

        // use non-strict '==' because int values from db becomes strings - should be fixed
        if ($offset_exists && $this->data[$offset] == $value) {
            return;
        }

        if (!isset($this->modified_data[$offset])) {
            $this->modified_data[$offset] = isset($this->data[$offset]) ? $this->data[$offset] : null;
            $this->modified[] = $offset;
        }
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        if (self::isTemplateVar($offset)) {
            return true;
        }
        if (array_key_exists($offset, $this->data)) {
            return true;
        }
        if (method_exists($this, '_get' . fx::util()->underscoreToCamel($offset))) {
            return true;
        }
        $event_res = fx::trigger('offsetExists', array('target' => $this, 'offset' => $offset));
        if ($event_res === true) {
            return true;
        }
        return isset(self::$_field_map[$this->getType()][$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    protected $_type = null;

    public function getType()
    {
        if (is_null($this->_type)) {
            $class = array_reverse(explode("\\", get_class($this)));
            $type = $class[1];
            $this->_type = $type;
        }
        return $this->_type;
    }

    /*
     * Add meta-data to be edited from the front
     * @param string $html the html code of record
     * @return string string with added meta-data
     */
    public function addTemplateRecordMeta($html, $collection, $index, $is_subroot)
    {
        return $html;
    }

    /**
     * Get meta info for the field in template
     * Here we handle only template vars, more complex implementation is in fx_content
     * @param string $field_keyword
     * @return array Meta info
     */
    public function getFieldMeta($field_keyword)
    {
        if (!self::isTemplateVar($field_keyword)) {
            return array();
        }
        $field_keyword = mb_substr($field_keyword, 1);
        return array(
            'var_type' => 'visual',
            'id'       => $field_keyword . '_' . $this['id'],
            'name'     => $field_keyword . '_' . $this['id'],
            // we need some more sophisticated way to guess the var type =)
            'type'     => 'string'
        );
    }

    public function isModified($field = null)
    {
        if ($field === null) {
            return count($this->modified) > 0;
        }
        if (!$this['id']) {
            return true;
        }
        return is_array($this->modified) && in_array($field, $this->modified);
    }
    
    public function setNotModified($field)
    {
        if (!$this->isModified($field)) {
            return $this;
        }
        unset ( $this->modified [array_search($field, $this->modified)]);
        return $this;
    }

    public function getOld($field)
    {
        if (!$this->isModified($field)) {
            return null;
        }
        return $this->modified_data[$field];
    }

    public function getModified()
    {
        return $this->modified;
    }
}

