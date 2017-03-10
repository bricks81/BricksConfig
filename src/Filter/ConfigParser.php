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

namespace Bricks\Config\Filter;

use Bricks\Event\ObserverInterface;
use Bricks\Config\Config;

class ConfigParser {

    /**
     * @param string $event
     * @param mixed $target
     * @param $data
     */
	public function dispatch($event,$target,$data){
		if(ConfigManager::EVENT_GET_AFTER == $event){
			if($this->isParsable($data->return)){
				$data->return = $this->parseConfigString($target,$data->return);
			}
		}
	}
	
	/**
	 * @param string $string
	 * @param string $open
	 * @return boolean
	 */
	public function isParsable($string,$open='{{'){
		return false !== strpos($string,$open)?true:false;
	}
	
	/**
	 * @param Config $cm
	 * @param string $string
	 * @param string $open
	 * @param string $close
	 * @param string $escape
	 * @param string $namespaceSeparator
	 * @return string
	 */
	public function parseConfigString(Config $cm,$string,$open='{{',$close='}}',$escape="\\",$namespaceSeparator="|"){
		$return = $string;
		$open = preg_quote($open,'/');
		$close = preg_quote($close,'/');
		$escape = preg_quote($escape,'/');
		$regexp = "$open(.*?|(?:$escape$close))$close";
		if(preg_match_all("/$regexp/ims",$string,$matches)){
			foreach($matches[1] AS $i => $match){
				$parts = explode($namespaceSeparator,$match);
				$count = count($parts);
				if(1==$count){
					$ret = $cm->get($parts[0]);										
				} elseif(2==$count){					
					$ret = $cm->get($parts[0],$parts[1]);
				}
				if($ret){
					$return = str_replace($matches[0][$i],$ret,$return);
				}
			}
		}		
		return $return;
	}
	
}