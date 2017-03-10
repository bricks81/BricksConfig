<?php

/**
 * Bricks Framework & Bricks CMS
 * http://bricks-cms.org
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 bricks-cms.org
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bricks\Config;

use Zend\Config\Config as ZConfig;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class Config
 * @package Bricks\Config
 */
class Config implements ConfigInterface,
    EventManagerAwareInterface {
	
	const EVENT_SET_BEFORE = __CLASS__.'::set.before';
	const EVENT_SET_AFTER = __CLASS__.'::set.after';
	const EVENT_GET_BEFORE = __CLASS__.'::get.before';
	const EVENT_GET_AFTER = __CLASS__.'::get.after';

    /**
     * @var ZConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $defaultNamespace = 'default';

    /**
     * @var array
     */
    protected $namespaces = array();

    /**
     * @var array
     */
	protected $readonly = array();

    /**
     * @var string
     */
	protected $pathSeparator = '.';

    /**
     * @var EventManagerInterface
     */
	protected $eventManager;

    /**
     * @param $config
     */
    public function setConfig($config){
	    $this->config = new ZConfig($config,true);
    }

    /**
     * @return ZConfig
     */
    public function getConfig(){
	    return $this->config;
    }

    /**
     * @param $namespace
     */
    public function setDefaultNamespace($namespace){
        $this->defaultNamespace = $namespace;
    }

    /**
     * @return string
     */
    public function getDefaultNamespace(){
        return $this->defaultNamespace;
    }

    /**
     * @param $namespace
     * @return bool
     */
    public function addNamespace($namespace){
        if(false === array_search($namespace,$this->namespaces)){
            $this->namespaces[] = $namespace;
            return true;
        }
        return false;
    }

    /**
     * @param $namespace
     * @return bool
     */
    public function removeNamespace($namespace){
        if(false !== ($key = array_serch($namespace,$this->namespaces))){
            unset($this->namespaces[$key]);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getNamespaces(){
        return $this->namespaces;
    }

    /**
     * @param EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager){
		$this->eventManager = $eventManager;
	}

    /**
     * @return EventManagerInterface
     */
    public function getEventManager(){
		return $this->eventManager;
	}

    /**
     * @param $path
     * @param null $namespace
     * @return array|null
     */
    public function get($path, $namespace=null){
		$namespace = $namespace?:$this->getDefaultNamespace();
		$em = $this->getEventManager();
        if(!is_array($namespace)){
            $namespaces = array($namespace);
        } else {
            $namespaces = $namespace;
        }
        if(false === array_search($this->getDefaultNamespace(),$namespaces)){
            array_unshift($namespaces,$this->getDefaultNamespace());
        }

		$this->getEventManager()->trigger(__CLASS__.'::'.__METHOD__.'.before',$this,[
		    'path' => &$path,
            'namespaces' => &$namespaces
        ]);

        $parts = explode($this->pathSeparator,$path);
        $count = count($parts);
		$return = null;
		foreach($namespaces AS $namespace){
            if(isset($this->config->bricks->$namespace)){
                $current = &$this->config->bricks->$namespace;
                $take = false;
                foreach($parts AS $i => $key){
                    if(isset($current->$key)){
                        if($i+1 >= $count){
                            $current = $current->$key;
                        } else {
                            $current = &$current->$key;
                        }
                        $take = true;
                    } else {
                        $take = false;
                    }
                }
                if($take){
                    $return = $current;
                }
            }
        }

        $return = $return instanceof ZConfig ? $return->toArray() : $return;

        $this->getEventManager()->trigger(__CLASS__.'::'.__METHOD__.'.after',$this,[
            'path' => &$path,
            'namespaces' => &$namespaces,
            'return' => &$return
        ]);

        return $return;
	}

    /**
     * @param $path
     * @param $value
     * @param null $namespace
     */
    public function set($path, $value, $namespace=null){
		$namespace = $namespace?:$this->defaultNamespace;

		$this->getEventManager()->trigger(__CLASS__.'::'.__METHOD__.'.before',[
            'path' => &$path,
            'value' => &$value,
            'namespace' => &$namespace
        ]);

		$parts = explode($this->pathSeparator,$path);

		if(!isset($this->config->bricks->$namespace)){
		    $this->config->bricks->$namespace = [];
        }
        $current = &$this->config->bricks->$namespace;
		$count = count($parts);
        foreach($parts AS $i => $key){
            if($i+1 >= $count){
                $current->$key = $value;
            } elseif(!isset($current->$key)){
                $current->$key = [];
            }
        }

        $this->getEventManager()->trigger(__CLASS__.'::'.__METHOD__.'.after',$this,[
            'path' => &$path,
            'value' => &$value,
            'namespace' => &$namespace
        ]);
	}
	
}