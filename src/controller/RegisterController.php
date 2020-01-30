<?php


namespace PhpOpdracht\Controller;


use PhpOpdracht\Maps\User;
use PhpOpdracht\Repository\UserReposetory;
use PhpOpdracht\Service\RegisterValidationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class RegisterController
{
    public function index(Request $request)
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);


        return $twig->render('register/index.twig');

    }

    public function newRegister(Request $request)
    {

        $validate = new RegisterValidationService();
        $validation = $validate->validate($request);

        if (count($validation) > 0) {
            $loader = new \Twig\Loader\FilesystemLoader('../templates');
            $twig = new \Twig\Environment($loader, ['debug' => true]);

            // add errors
            return $twig->render('register/index.twig');
        }

        $user = new User();


        $user->setFirstName($request->get('firstname'));

        $user->setLastName($request->get('surname'));
        $user->setEmail($request->get('Email'));
        $user->setSecretMessage($request->get('secret'));
        $password = password_hash($request->get('password'), PASSWORD_DEFAULT);
        $user->setPassword($password);
        unset($password);

        $repo = new UserReposetory();

        $userSaved = $repo->saveUser($user);


        if ($userSaved === false) {
            throw new \ErrorException('somthing went wrong saving the user');
        }

        $user->setId((int)$userSaved);

        setcookie('userId', $user->getId());

        //redirect zoals het er staat
        return new RedirectResponse('/');
    }
}