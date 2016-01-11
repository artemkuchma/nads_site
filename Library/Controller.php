<?php

abstract class Controller {

    private function file_path()
    {
        $tplDir= Router::getController();
        $tplName = Router::getAction();
        $templateFile=VIEW_DIR.$tplDir.DS.$tplName.'.phtml';
        if(!file_exists($templateFile)){
            throw new Exception("{$templateFile} not found", 404);
        }
        return $templateFile;

    }
    protected  function render(array $args=array())
    {
        extract($args);

        ob_start();
        require $this->file_path(); //$templateFile;
        $content = ob_get_clean();

        ob_start();
        require VIEW_DIR. 'layout.phtml';
        return ob_get_clean();

    }
    public static  function redirect($url)
    {
        header("Location: $url");
        die;
       // exit;

    }

    public  static function rewrite_file($file_path, $mode, $date)
    {
        $f = fopen($file_path,$mode);
        fwrite($f, $date);
        fclose($f);
    }

    public static function rewrite_file_alias()
    {
        $aliasModel = new AliasModel();
        $date = '<?php'.PHP_EOL. '$url_alias = array('.PHP_EOL;
        foreach($aliasModel->getAliasDate() as $k => $v){
            $date .=''.PHP_EOL.'      '.$k.' => array('.PHP_EOL;
            foreach($v as $key => $val){
                $date .="            '". $key."' => '".$val."',".PHP_EOL;
            }
            $date .='      ),'.PHP_EOL.'';
    }
        $date .= ');';

        self::rewrite_file(LIB_DIR.'aliass.php','w', $date);
    }



}