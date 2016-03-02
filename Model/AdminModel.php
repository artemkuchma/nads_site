<?php


class AdminModel
{

    public function getAdminPage($id)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT p.id, p.status, p.controller, p.action, a.name, a.text, a.alias  FROM pages p JOIN admin a ON p.id = a.id_page AND p.id = :id";
        $placeholders = array('id' => $id);
        $date = $dbc->getDate($sql, $placeholders);
        if (!$date || $date[0]['status'] == 0) {
            throw new Exception("id = $id ,is not exist", 404);
        }
        return $date;
    }

    public function getLogPage()
    {
        if (file_exists(WEBROOT_DIR . 'log.txt')) {
            $data = file_get_contents(WEBROOT_DIR . 'log.txt');
            return $data;
        }
        return null;
    }

    public function getStaticTranslation()
    {
        return include(LANG_DIR . 'translation.php');
    }

    public function insertStaticTranslation()
    {

        $dbc = Connect::getConnection();
        $placeholders = array();
        foreach ($this->getStaticTranslation() as $k => $v) {
            $sql = "INSERT INTO `static_translation` (`key`, `text_en`, `text_uk`) VALUES ('" . $k . "', '" . $v['en'] . "', '" . $v['uk'] . "')";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);
        }


    }

    public function getDBStaticTranslation()
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT `id`,`key`,`text_en`, `text_uk` FROM `static_translation`";
        $placeholders = array();
        $data = $dbc->getDate($sql, $placeholders);

        return $data;
    }
    public function getStaticTranslationByKey($key)
    {
        $dbc = Connect::getConnection();
        $placeholders = array(
            'key' => $key
        );
        $sql = "SELECT `id`,`text_en`, `text_uk` FROM `static_translation` WHERE `key`= :key";

        $data = $dbc->getDate($sql, $placeholders);

        return $data[0];

    }

    public function updateStaticTranslation(Request $request)
    {
        $dbc = Connect::getConnection();
        $text_en = str_replace("'", " ", $request->post('text_en'));
        $text_uk = str_replace("'", " ", $request->post('text_uk'));

        $text_en = $request->post('text_en');
        $text_uk = $request->post('text_uk');
        $placeholders = array(
            'key'=> $request->post('key'),
            'text_en' => $text_en,
            'text_uk' => $text_uk
        );
        Debugger::PrintR($placeholders);
        $sql = "UPDATE `static_translation` SET `text_en`= :text_en,`text_uk`= :text_uk WHERE `key`= :key";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

    }


}