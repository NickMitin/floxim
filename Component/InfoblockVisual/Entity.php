<?php

namespace Floxim\Floxim\Component\InfoblockVisual;

use Floxim\Floxim\System;
use Floxim\Floxim\System\Fx as fx;

class Entity extends System\Entity
{
    protected $needRecountFiles = true;

    protected function beforeSave()
    {
        parent::beforeSave();
        unset($this['is_stub']);
        if (!$this['priority'] && $this['layout_id']) {
            $last_vis = fx::data('infoblock_visual')
                ->where('layout_id', $this['layout_id'])
                ->where('area', $this['area'])
                ->order(null)
                ->order('priority', 'desc')
                ->one();
            $this['priority'] = $last_vis['priority'] + 1;
        }
        
        if ($this->needRecountFiles) {
            $this->recountFiles();
        }
    }

    public function setNeedRecountFiles($need)
    {
        $this->needRecountFiles = $need;
    }

    public function recountFiles()
    {
        $modified_params = $this->getModifiedParams();
        $fxPath = fx::path();
        foreach ($modified_params as $field => $params) {
            $all_params = $this[$field];
            foreach ($params as $pk => $pv) {
                if (self::checkValueIsFile($pv['new'])) {
                    fx::log($pv, 'is file');
                    $ib = $this['infoblock'];
                    $site_id = $ib ? $ib['site_id'] : fx::env('site_id');

                    $file_name = $fxPath->fileName($pv['new']);
                    $new_path = $fxPath->abs('@content_files/' . $site_id . '/visual/' . $file_name);
                    
                    $move_from = $fxPath->abs($pv['new']);
                    
                    if (file_exists($move_from)) {
                        fx::files()->move($move_from, $new_path);
                        $all_params[$pk] = $fxPath->removeBase($fxPath->http($new_path));
                    }
                }
                if ($pv['old']) {
                    $old_path = $fxPath->abs(FX_BASE_URL.$pv['old']);
                    if (self::checkValueIsFile($old_path)) {
                        fx::files()->rm($old_path);
                    }
                }
            }
            $this[$field] = $all_params;
        }
    }

    protected function beforeDelete()
    {
        parent::beforeDelete();
        $files = $this->getFileParams();
        foreach ($files as $f) {
            fx::files()->rm($f);
        }
    }

    /**
     * find file paths inside params collection and drop them
     */
    public function getFileParams(System\Collection $params = null)
    {
        if (!$params) {
            $params = fx::collection($this['template_visual'])->concat($this['wrapper_visual']);
        }
        $res = array();
        foreach ($params as $p) {
            if (self::checkValueIsFile($p)) {
                $res [] = $p;
            }
        }
        return $res;
    }

    public static function checkValueIsFile($v)
    {
        if (empty($v)) {
            return false;
        }
        $files_path = fx::path('@files');
        $path = fx::path();
        return $path->isInside($v, $files_path) && $path->isFile($v);
    }

    public function getModifiedParams()
    {
        $res = array();
        foreach (array('template_visual', 'wrapper_visual') as $field) {
            $res[$field] = array();
            if (!$this->isModified($field)) {
                continue;
            }
            $new = $this[$field];
            if (!$new) {
                $new = array();
            }
            $old = $this->getOld($field);
            if (!$old || !is_array($old)) {
                $old = array();
            }
            $keys = array_unique(
                array_merge(
                    array_keys($old),
                    array_keys($new)
                )
            );
            foreach ($keys as $key) {
                if (isset($old[$key]) && isset($new[$key]) && $old[$key] == $new[$key]) {
                    continue;
                }
                $res[$field][$key] = array(
                    'old' => isset($old[$key]) ? $old[$key] : null,
                    'new' => isset($new[$key]) ? $new[$key] : null
                );
            }
        }
        return $res;
    }
}