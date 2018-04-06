<?php

class Parser
{
    public $arLinks;
    public function __construct()
    {
        $linksStr=file_get_contents($_SERVER['DOCUMENT_ROOT']."/links.txt");
        $this->arLinks=explode(';',$linksStr);
    }
    
    public function GetPage($url):string{
        return file_get_contents($url);
    }
}