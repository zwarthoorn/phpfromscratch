<?php


namespace PhpOpdracht\Controller;


use PhpOpdracht\Maps\User;
use PhpOpdracht\Repository\UserReposetory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController
{

    public function edit(Request $request, $id)
    {

        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);

        $user = $request->get('user');

        if ($user === null) {
            return new RedirectResponse('/');
        }

        $userToEdit = $user;
        if ($user->getAdmin() === 1) {
            $repo = new UserReposetory();

            $userToEdit = $repo->find('id', (int)$id);
        }

        if ((int)$id !== $user->getId() && $user->getAdmin() === 0) {
            setcookie('userId', '');
            return new RedirectResponse('/');
        }


        return $twig->render(
            'user/edit.twig',
            [
                'firstname' => $userToEdit->getFirstName(),
                'lastname' => $userToEdit->getLastName(),
                'email' => $userToEdit->getEmail(),
                'userId' => $userToEdit->getId(),
            ]
        );
    }

    public function save(Request $request, $id)
    {

        $user = $request->get('user');

        if ($user === null) {
            return new RedirectResponse('/');
        }


        $userToEdit = $id;

        if ((int)$userToEdit !== $user->getId() && $user->getAdmin() === 0) {
            return new RedirectResponse('/');
        }
        $repo = new UserReposetory();
        if ($userToEdit !== null) {
            $user = $repo->find('id', (int)$userToEdit);
        }

        $user->setFirstName($request->get('firstname'));
        $user->setLastName($request->get('surname'));
        $user->setEmail($request->get('Email'));


        if ($request->get('secret') !== '') {
            $user->setSecretMessage($request->get('secret'));
        }

        $newPass = false;
        if ($request->get('password') !== '') {
            $user->setPassword($request->get('password'));
            $newPass = true;
        }

        $update = $repo->updateUser($user, $newPass);


        if ($user->getAdmin() === 1) {
            return new RedirectResponse('/list');
        }
        return new RedirectResponse('/');

    }

    public function userList(Request $request)
    {

        $loader = new \Twig\Loader\FilesystemLoader('../templates');
        $twig = new \Twig\Environment($loader, ['debug' => true]);
        $user = $request->get('user');
        if ($user->getAdmin() !== 1) {
            return new RedirectResponse('/');
        }

        $searchValue = $request->get('search');
        $column = $request->get('column');

        $repo = new UserReposetory();
        if ($searchValue !== null) {
           $users = $repo->findAllLike($column, $searchValue);
        } else {

            //find evrithing
            $users = $repo->findAll();


        }

        return $twig->render(
            'user/list.twig',
            [
                'users' => $users
            ]
        );

    }

    public function makeAdmin(Request $request, $id)
    {
        $user = $request->get('user');
        if ($user->getAdmin() !== 1) {
            return new RedirectResponse('/');
        }

        $repo = new UserReposetory();
        $repo->toggleAdmin((int)$id);

        return new RedirectResponse('/');

    }

    public function delete(Request $request, $id)
    {
        $user = $request->get('user');
        if ($user->getAdmin() !== 1) {
            return new RedirectResponse('/');
        }

        $repo = new UserReposetory();
        $repo->toggleDeleted((int)$id);

        return new RedirectResponse('/');
    }

    public function logout(Request $request)
    {
        setcookie('userId', '', time() - 3600);

        return new RedirectResponse('/');

    }

}