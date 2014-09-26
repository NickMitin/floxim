<?php
namespace Floxim\Floxim\Component\Component;

use Floxim\Floxim\System;
use Floxim\Floxim\System\Fx as fx;

class Entity extends System\Entity {
    
    public function get_content_table() {
        return $this['keyword'] == 'content' ? $this['keyword'] : 'content_'.$this['keyword'];
    }
    
    public function get_chain($up_to_down = true) {
        $chain = array($this);
        $c_pid = $this->get('parent_id');
        while ($c_pid != 0) {
            $c_parent = fx::data('component', $c_pid);
            $chain []= $c_parent;
            $c_pid = $c_parent['parent_id'];
        }
        
        return $up_to_down ? array_reverse($chain) : $chain;
    }
    
    public function get_ancestors() {
        return array_slice($this->get_chain(false), 1);
    }

    protected $_class_id;

    public function __construct($input = array()) {
        parent::__construct($input);

        $this->_class_id = $this->data['id'];
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => fx::alang('Component name can not be empty','system'));
            $res = false;
        }

        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('Specify component keyword','system'));
            $res = false;
        }

        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9_-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('Keyword can only contain letters, numbers, symbols, "hyphen" and "underscore"','system'));
            $res = false;
        }

        if ($this['keyword']) {
            $components = fx::data('component')->all();
            foreach ($components as $component) {
                if ($component['id'] != $this['id'] && $component['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => fx::alang('This keyword is used by the component','system') . ' "'.$component['name'].'"');
                    $res = false;
                }
            }
        }


        return $res;
    }

    protected $_stored_fields = null;
    public function fields() {
        return $this['fields'];
    }
    
    public function all_fields() {
        $fields = new System\Collection();
        foreach ($this->get_chain() as $component) {
            $fields->concat($component->fields());
        }
        return $fields;
    }

    public function get_field_by_keyword($keyword,$use_chain=false) {
        if ($use_chain) {
            $fields=$this->all_fields();
        } else {
            $fields=$this->fields();
        }
        foreach($fields as $field) {
            if (strtolower($field['keyword'])==strtolower($keyword)) {
                return $field;
            }
        }
        return null;
    }

    public function get_sortable_fields() {
        //$this->_load_fields();

        $result = array();

        $result['created'] = fx::alang('Created','system');
        $result['id'] = 'ID';
        $result['priority'] = fx::alang('Priority','system');


        foreach ($this->fields() as $v) {
            $result[$v['name']] = $v['description'];
        }

        return $result;
    }

    public function is_user_component() {
        return $this['keyword'] == 'user';
    }

    protected function _after_insert() {
        $this->create_content_table();
    }
    
    public function create_content_table() {
        $sql = "DROP TABLE IF  EXISTS `{{content_".$this['keyword']."}}`;
            CREATE TABLE IF NOT EXISTS `{{content_".$this['keyword']."}}` (
            `id` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        fx::db()->query($sql);
    }

    protected function _before_delete() {
        if ($this['children']) {
            foreach ($this['children'] as $child_com) {
                $child_com->delete();
            }
        }
        $this->delete_fields();
        $this->delete_infoblocks();
        $this->delete_content_table();
        $this->delete_files();
    }

    protected function delete_fields() {
        foreach ($this->fields() as $field) {
            $field->delete();
        }
    }

    protected function delete_files() {
        $base_path = fx::path(
                ($this['vendor'] === 'std') ? 'std' : 'root',
                'component/'.$this['keyword'].'/'
            ).'/';
        fx::files()->rm($base_path);
    }

    protected function delete_content_table() {
        $contents = fx::data('content_'.$this['keyword'])->all();
        foreach ($contents as $content) {
            $content->delete();
        }
        $sql = "DROP TABLE `{{content_".$this['keyword']."}}`";
        fx::db()->query($sql);
    }

    protected function delete_infoblocks() {
        $infoblocks = fx::data('infoblock')->where('controller', 'component_'.$this['keyword'])->all();
        foreach ($infoblocks as $infoblock) {
            $infoblock->delete();
        }
    }
    /**
     * Get collection of all component's descendants
     * @return \Floxim\Floxim\System\Collection
     */
    public function get_all_children() {
        $res = fx::collection()->concat($this['children']);
        foreach ($res as $child) {
            $res->concat($child->get_all_children());
        }
        return $res;
    }
    
    /**
     * Get collection of all component's descendants and the component itself
     * @return \Floxim\Floxim\System\Collection
     */
    public function get_all_variants() {
        $res = fx::collection($this);
        $res->concat($this->get_all_children());
        return $res;
    }
    
    public function scaffold() {
        $keyword = $this['keyword'];
        $base_path = fx::path(
            ($this['vendor'] === 'std') ? 'std' : 'root', 
            'component/'.$keyword.'/'
        ).'/';
        
        $controller_file = $base_path.$keyword.'.php';
        
        $parent_com = fx::data('component', $this['parent_id']);
        $parent_ctr = fx::controller($parent_com['keyword']);
        $parent_ctr_class = get_class($parent_ctr);
        
        $parent_finder = fx::content($parent_com['keyword']);
        $parent_finder_class = get_class($parent_finder);
        
        $parent_entity = $parent_finder->create();
        
        $parent_entity_class = get_class($parent_entity);
        ob_start();
        // todo: psr0 need fix
        echo "<?php\n";?>
class fx_controller_component_<?php echo  $keyword; ?> extends <?php echo $parent_ctr_class; ?> {
    // create component controller logic
}<?php
        $code = ob_get_clean();
        fx::files()->writefile($controller_file, $code);
        
        $finder_file = $base_path.$keyword.'.data.php';
        ob_start();
        echo "<?php\n";?>
class fx_data_content_<?php echo  $keyword; ?> extends <?php echo $parent_finder_class; ?> {
    // create component finder logic
}<?php
        $code = ob_get_clean();
        fx::files()->writefile($finder_file, $code);
        
        $entity_file = $base_path.$keyword.'.entity.php';
        ob_start();
        echo "<?php\n";?>
class fx_content_<?php echo  $keyword; ?> extends <?php echo $parent_entity_class; ?> {
    // create component finder logic
}<?php
        $code = ob_get_clean();
        fx::files()->writefile($entity_file, $code);
    }
}