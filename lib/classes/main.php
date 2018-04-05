<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 05.04.18
 * Time: 14:58
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/phpquery/phpQuery.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/classes/parser.php');


class Main
{
    public $arConfig;
    
    public function execute()
    {
        $paser = new Parser();
        
        foreach ($paser->arLinks as $arLink) {
            $arLink           = trim($arLink);
            $document         = phpQuery::newDocument($paser->GetPage($arLink));
            $this->arConfig[] = $this->getComplectations($document);
        }
        
        $this->arConfig = $this->sortArConfig($this->arConfig);
        echo '<pre>';
        print_r($this->arConfig);
        echo '</pre>';
    }
    
    private function getComplectations($page): array
    {
        $configurator = $page->find('[itemprop="offers"]');
        $arConfigs    = [];
        foreach ($configurator as $config) {
            $tempConfig                    = pq($config);
            $configId                      = $tempConfig->attr('id');
            $arConfigs[$configId]['ID']    = $configId;
            $arConfigs[$configId]['PRICE'] = $tempConfig->attr('price');
            $arConfigs[$configId]['NAME']  = $tempConfig->find('.kompl_name')->html();
            $descriptorNames               = $page->find("[kompl_id=$configId]");
            
            $i = 0;
            foreach ($descriptorNames as $descItem) {
                $tempItem = pq($descItem);
                if ($i == 0) {
                    $i++;
                    continue;
                }
                $arNames[$i++] = $tempItem->html();
            }
            
            $descriptor = $page->find(".kompl_" . $configId);
            $i          = 1;
            foreach ($descriptor as $descItem) {
                $tempItem                                      = pq($descItem);
                $arConfigs[$configId]['OPTIONS'][$arNames[$i]] = explode('•', $tempItem->html());
                unset($arConfigs[$configId]['OPTIONS'][$arNames[$i++]][0]);
            }
        }
        return $arConfigs;
    }
    
    private function sortArConfig(array $arConfigs): array
    {
        if (count($arConfigs) <= 0) {
            return [];
        }
        $arSorted = [];
        foreach ($arConfigs as $arConfig) {
            foreach ($arConfig as $arItem) {
                $arSorted[$arItem['ID']] = $arItem;
            }
        }
        return $arSorted;
    }
}