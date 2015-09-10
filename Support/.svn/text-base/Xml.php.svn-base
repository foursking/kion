<?php

namespace Setsuna\Support;



/*
* 从数组生成XML文件
*/
class Xml
{
        
    public function __construct(){
    
    }

 
    public function getXML($array){
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?><plist version="1.0"><dict>';
        $xmlString.=$this->make($array);
        return $xmlString . "</dict></plist>";
    }
    
    /*
     * 递归生成XML字串
     */
    public function make($array)
    {
        $xmlString = '';

        foreach($array as $index => $value) {
            $xmlString .= '<key>' . $index . '</key>';
            $xmlString .= "<{$value['type']}>" . $value['value'] . "</{$value['type']}>";
        }
        return $xmlString;
    }
    
    /**
     * 将字串保存到文件
     * @param $fileName 文件名
     * @param $XMLString 已经生成的XML字串
     */
    public function saveToFile($fileName,$XMLString)
    {
        if(!$handle=fopen($fileName,'w'))
        {
            return FALSE;
        }
        if(!fwrite($handle,$XMLString))
        {
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * 直接通过数组生成XML文件
     */
    public function write($fileName,$array,$xslName=''){
        $XMLString=$this->getXML($array,$xslName);
        $result=$this->saveToFile($fileName,$XMLString);
        return $result;
    }
}
