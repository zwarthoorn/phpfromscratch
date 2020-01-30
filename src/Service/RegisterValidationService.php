<?php


namespace PhpOpdracht\Service;


use PhpOpdracht\Maps\User;
use PhpOpdracht\Repository\UserReposetory;
use Symfony\Component\HttpFoundation\Request;

class RegisterValidationService
{
    public function validate(Request $request)
    {

        $firstname = $request->get('firstname');
        $password = $request->get('password');
        $email = $request->get('Email');
        $lastname = $request->get('surname');


        $error = [];

        $firstnameValidation = $this->validateFirstName($firstname);

        if ($firstnameValidation['error'] === true) {
            $error[] = $firstnameValidation;
        }

        $lastnameValidation = $this->lastNameValidation($lastname);

        if ($lastnameValidation['error']) {
            $error[] = $lastnameValidation;
        }

        $checkEmail = $this->checkEmail($email);

        if ($checkEmail['error']) {
            $error[] = $checkEmail;
        }

        $validatePassword = $this->validatePassword($password);

        if ($validatePassword['error']) {
            $error[] = $checkEmail;
        }

        return $error;
    }

    private function validateFirstName($firstname)
    {
        $validateLenght = $this->validateLenght($firstname);

        if ($validateLenght === false) {
            return [
                'message' => 'uw voornaam is of te lang of te kort het minimum is 2 maximum is 20',
                'error' => true
            ];
        }

        $validateNormalText = $this->validateNormalText($firstname);

        if ($validateNormalText === false) {
            return [
                'message' => 'u heeft apparte tekens in uw naam die niet mogen',
                'error' => true
            ];
        }

        return [
            'message' => '',
            'error' => false
        ];

    }

    private function validateLenght($text)
    {
        $length = strlen($text);

        if ($length <= 1 || $length > 20) {
            return false;
        }

        return true;

    }

    private function validateNormalText($text)
    {
        if (preg_match('/[^A-Za-z]/', $text) === 0) {
            return true;
        }

        return false;
    }

    private function lastNameValidation($lastname)
    {
        $validateLenght = $this->validateLenght($lastname);

        if ($validateLenght === false) {
            return [
                'message' => 'uw achternaam is of te lang of te kort het minimum is 2 maximum is 20',
                'error' => true
            ];
        }

        $validateNormalText = $this->validateNormalText($lastname);

        if ($validateNormalText === false) {
            return [
                'message' => 'u heeft apparte tekens in uw achternaamnaam die niet mogen',
                'error' => true
            ];
        }

        return [
            'message' => '',
            'error' => false
        ];

    }

    private function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return [
                'message' => 'uw email adress klopt niet met een standaard email adres',
                'error' => true
            ];

        }

        $reposetory = new UserReposetory();;
        $user = $reposetory->find('email', $email);


        if ($user instanceof User)
        {
            return [
                'message' => 'Email adres al in gebruik',
                'error' => true
            ];
        }
        return [
            'message' => '',
            'error' => false
        ];

    }

    private function validatePassword($password)
    {
        $validateLenght = $this->validateLenght($password);

        if ($validateLenght === false) {
            return [
                'message' => 'uw achternaam is of te lang of te kort het minimum is 2 maximum is 20',
                'error' => true
            ];
        }
        return [
            'message' => '',
            'error' => false
        ];
    }

}