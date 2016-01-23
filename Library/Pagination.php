<?php


class Pagination {
    public $buttons = array();
    public function __construct($currentPage, $itemsCount, $itemsPerPage)
    {

        if(!$currentPage){
            return;
        }
        $pageCount = ceil($itemsCount/$itemsPerPage);
        if($pageCount ==1){
            return;
        }
        if($currentPage>$pageCount){
            $currentPage = $pageCount;
        }
        $this->buttons[] = new Battons($currentPage-1, $currentPage>1, __t('Previous'));

        for($i=1; $i<=$pageCount; $i++){
            $isActive = $currentPage != $i;
            $this->buttons[] = new Battons($i, $isActive);
        }

        $this->buttons[] = new Battons($currentPage+1, $currentPage < $pageCount, __t('Next'));

    }

}