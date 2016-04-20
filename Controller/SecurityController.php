<?php


class SecurityController extends Controller
{

    public function registerAction(Request $request)
    {
        $registerForm = new RegisterModel($request);

        if ($request->isPost()) {
            if ($registerForm->isValid()) {
                if ($registerForm->usernameValid()) {
                    if ($registerForm->passwordStrong()) {

                        if ($registerForm->isUserExist()) {
                            $registerForm->insertIntoDB();
                            Session::setFlash(__t('successfully_logged'));
                            $this->redirect("/");
                        } else {

                            Session::setFlash( __t('user_already_exists'));
                        }
                    } else {
                        Session::setFlash( __t('password_not_strong'));
                    }
                } else {
                    Session::setFlash( __t('invalid_login'));
                }
            } else {
                Session::setFlash($registerForm->passwordMath() ? __t('fill_fields') : __t('passwords_dont_match'));

            }
        }
        $args = array(
            'registerForm' => $registerForm,
        );
        return $this->render($args);
    }

    public function loginAction(Request $request)
    {

        $login = new LoginModel($request);
        if ($request->isPost()) {
            if ($login->isValid()) {
                if ($login->getUser()) {
                    $user = array(
                        'user' => $login->getUser()[0]['username'],
                        'id' => $login->getUser()[0]['id']
                    );
                    Session::set('user', $user);
                    Session::setFlash( __t('logged_in'));
                    $this->redirect("/");

                } else {

                    Session::setFlash( __t('login_or_password_incorrect'));
                }
            } else {
                Session::setFlash( __t('fill_fields'));
            }
        }

        $img_default_url = 'Webroot/uploads/images/'.Config::get('default_img');

        $args = array(
            'login' => $login,
            'img' => $img_default_url
        );
        return $this->render($args);
    }

    public function logoutAction($key = 'user')
    {
        Session::remove($key);
        Session::destroy();
        Session::setFlash( __t('you_logout'));
        $this->redirect("/");
    }

    // выводит весь блок регистрации(ссылки логин, логаут, регистрация)
    public function logAction()
    {
        $args = array();
        require LIB_DIR . 'patterns.php';
        $args['url_register'] ='/'.Router::getLanguage().'/'. $url_patterns['register']['pattern_' . Router::getLanguage()];
        $args['url_login'] = '/'.Router::getLanguage().'/'.$url_patterns['login']['pattern_' . Router::getLanguage()];
        $args['url_logout'] = '/'.Router::getLanguage().'/'.$url_patterns['logout']['pattern_' . Router::getLanguage()];

        return $this->render_login_logout($args);
    }


}