<?php


class UploadFile
{

    private $name;
    private $valid_types;
    private $max_image_width;
    private $max_image_height;
    private $max_image_size;
    private $request;

    public function __construct(Request $request, array $data_file = null)
    {
        $this->request = $request;
        If (isset($data_file)) {
            $this->name = isset($data_file['name']) ? $data_file['name'] : $request->files('name');
            $this->valid_types = isset($data_file['valid_types']) ? $data_file['valid_types'] : Config::get('image_types');
            $this->max_image_width = isset($data_file['max_image_width']) ? $data_file['max_image_width'] : 300;
            $this->max_image_height = isset($data_file['max_image_height']) ? $data_file['max_image_height'] : 300;
            $this->max_image_size = isset($data_file['max_image_size']) ? $data_file['max_image_size'] : 2000000;
        } else {
            $this->name = $request->files('name');
            $this->valid_types = Config::get('image_types');
            $this->max_image_width = 300;
            $this->max_image_height = 300;
            $this->max_image_size = 2000000;
        }
    }

    public function uploadImg(Request $request)
    {
        $redirect_status = null;
        if ($request->isUserfile()) {
            if (is_uploaded_file($request->files('tmp_name'))) {
                $filename = $request->files('tmp_name');
                $ext = substr($request->files('name'),
                    1 + strrpos($request->files('name'), "."));
                if (filesize($filename) > $this->max_image_size) {
                    Session::setFlash('Размер файла слишком большой');

                } elseif (!in_array($ext, $this->valid_types)) {
                    Session::setFlash('Недопустимый тип файла');

                } else {
                    //   $size = getimagesize($filename);

                    //   if (($size) && ($size[0] < $this->max_image_width)
                    //     && ($size[1] < $this->max_image_height)) {


                    $name = $request->files('name');
                    $file_path = WEBROOT_DIR . "uploads/images/$name";
                    $file_thumbs = WEBROOT_DIR . "uploads/.thumbs/images/$name";

                    if (move_uploaded_file($filename, $file_path)) {

                        $this->resize($file_path, $file_path, Config::get('img_width'), 0);
                        $y = $this->get_x_y_point($file_path, Config::get('img_height'));

                        $this->crop($file_path, $file_path, array(0, $y, Config::get('img_width'), Config::get('img_height') + $y));
                        $this->resize($file_path, $file_thumbs, 200, 0);
                        Session::setFlash('Файл успешно загружен');
                        $redirect_status = 1;
                        return $redirect_status;

                    } else {
                        Session::setFlash('Файл не загружен');

                    }
                    // } else {

                    //    Session::setFlash('Недопустимые размеры фотографии');

                    //    }
                }
            } else {
                Session::setFlash('Пустой файл');
                $redirect_status = 1;
                return $redirect_status;

            }
        } else {
            Session::setFlash('Файл не выбран');
            ;

        }
        return $redirect_status;
    }


    private function get_x_y_point($file_input, $h_o)
    {
        list($w_i, $h_i) = getimagesize($file_input);
        $y = (($h_i - $h_o) / 2);
        return $y;
    }

    public function resize($file_input, $file_output, $w_o, $h_o, $percent = false)
    {
        list($w_i, $h_i, $type) = getimagesize($file_input);
        if (!$w_i || !$h_i) {
            echo 'Невозможно получить длину и ширину изображения';
            return;
        }

        $types = array('', 'gif', 'jpeg', 'png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom' . $ext;
            $img = $func($file_input);
        } else {
            echo 'Некорректный формат файла';
            return;
        }
        if ($percent) {
            $w_o *= $w_i / 100;
            $h_o *= $h_i / 100;
        }

        if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
        if (!$w_o) $w_o = $h_o / ($h_i / $w_i);


        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
        if ($type == 2) {
            return imagejpeg($img_o, $file_output, 100);
        } else {
            $func = 'images' . $ext;
            return $func($img_o, $file_output);
        }

    }

    public function crop($file_input, $file_output, $crop = 'square', $percent = false)
    {
        list($w_i, $h_i, $type) = getimagesize($file_input);
        if (!$w_i || !$h_i) {
            echo 'Невозможно получить длину и ширину изображения';
            return;
        }
        $types = array('', 'gif', 'jpeg', 'png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom' . $ext;
            $img = $func($file_input);
        } else {
            echo 'Некорректный формат файла';
            return;
        }
        if ($crop == 'square') {
            $min = $w_i;
            if ($w_i > $h_i) $min = $h_i;
            $w_o = $h_o = $min;
        } else {
            list($x_o, $y_o, $w_o, $h_o) = $crop;
            if ($percent) {
                $w_o *= $w_i / 100;
                $h_o *= $h_i / 100;
                $x_o *= $w_i / 100;
                $y_o *= $h_i / 100;
            }
            if ($w_o < 0) $w_o += $w_i;
            $w_o -= $x_o;
            if ($h_o < 0) $h_o += $h_i;
            $h_o -= $y_o;
        }
        $img_o = imagecreatetruecolor($w_o, $h_o);
        imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
        if ($type == 2) {
            return imagejpeg($img_o, $file_output, 100);
        } else {
            $func = 'images' . $ext;
            return $func($img_o, $file_output);
        }

    }

    public function getImgDirArray()
    {
        $dir = WEBROOT_DIR . 'uploads/images';
        $images_n = scandir($dir);
        unset($images_n[0]);
        unset($images_n[1]);

        $images_name = array();
        $images_url = array();
        $thumbs_url = array();
        foreach ($images_n as $v) {
            $img_size = getimagesize(WEBROOT_DIR . 'uploads/images/' . $v);
            if ($img_size[0] == Config::get('img_width') && $img_size[1] == Config::get('img_height')) {
                $images_url[] = 'Webroot/uploads/images/' . $v;
                $thumbs_url[] = '/Webroot/uploads/.thumbs/images/' . $v;
                $images_name[] = $v;
            }
        }
        $request = new Request();

        // Debugger::PrintR($images_name);
        // Debugger::PrintR($thumbs_url);
        $args = array(
            'name' => $images_name,
            'url' => $images_url,
            'thumbs_url' => $thumbs_url,
            'request' => $request
        );
        return $args;
    }

}