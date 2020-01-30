<?php


namespace PhpOpdracht\Controller;

use PhpOpdracht\Maps\User;
use PhpOpdracht\Repository\UserReposetory;
use PhpOpdracht\Utilz\StringService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class HomeController
{
    public function index(Request $request)
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);

        $user = $request->get('user');

        if ($user instanceof User)
        {
            if ($user->getAdmin() === 1)
            {
                return new RedirectResponse('/list');
            }
            return new RedirectResponse('/edit/'.$user->getId());
        }

        return $twig->render('index/index.twig');

    }

    public function login(Request $request)
    {
        $user = $request->get('user');

        if ($user instanceof User)
        {
            if ($user->getAdmin() === 1)
            {
                return new RedirectResponse('/list');
            }
            return new RedirectResponse('/edit/'.$user->getId());
        }

        $repo = new UserReposetory();
        $userToTest = $repo->find('email', $request->get('email'));

        if ($userToTest === null)
        {
            return new RedirectResponse('/');
        }


        if (password_verify($request->get('password'), $userToTest->getPassword()))
        {
            setcookie('userId', (string)$userToTest->getId());
            return new RedirectResponse('/edit/'.$userToTest->getId());
        }

        return new RedirectResponse('/');
    }

    public function forgot(Request $request)
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $user = $request->get('user');
        if ($user instanceof User)
        {
            if ($user->getAdmin() === 1)
            {
                return new RedirectResponse('/list');
            }
            return new RedirectResponse('/edit/'.$user->getId());
        }

        return $twig->render('index/forgot.twig');
    }

    public function getNewPassword(Request $request)
    {
        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $user = $request->get('user');
        if ($user instanceof User)
        {
            if ($user->getAdmin() === 1)
            {
                return new RedirectResponse('/list');
            }
            return new RedirectResponse('/edit/'.$user->getId());
        }

        $repo = new UserReposetory();

        $user = $repo->find('email', $request->get('email'));

        if ($user === null)
        {
            return new RedirectResponse('/');
        }

        $password = 'geen niew password fout in secret';
        if ($user->getSecretMessage() === $request->get('secret'))
        {
            $password = StringService::randomString(8);

            $user->setPassword($password);

            $repo->updateUser($user,true);
        }

        return $twig->render('index/password.twig',['password'=>$password]);


    }
}