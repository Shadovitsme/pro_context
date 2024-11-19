<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\error;

class UserController extends Controller
{
    public function outputResult($queryResult = null, ?string $err = null, ?string $ok = null)
    {
        $result = [];
        if ($queryResult) {
            $result['users'] = [
                'id' => $queryResult->id,
                'login' => $queryResult->name,
                'password' => $queryResult->password,
                'birth_date' => $queryResult->birth_date,
                'email' => $queryResult->email,
                'male' => $queryResult->male,
            ];
        }

        if ($err) {
            $result = array_merge(['error' => $err], $result);
            http_response_code(404);
        }

        if ($ok) {
            $result = array_merge(['ok' => $ok], $result);
        }

        echo (json_encode($result));
        return;
    }
    public function checkZeroArray($queryResult)
    {
        if (!empty($queryResult[0]->id)) {
            $this->outputResult($queryResult[0]);
        } else {
            $this->outputResult(err: 'no such user');
        };
    }
    function checkPassword(?string $password = null): bool
    {
        if ($password === null) {
            $this->outputResult(err: 'no password provided');
            return false;
        }
        if (strlen($password) < 5) {
            $this->outputResult(err: 'password must be at least 6 characters');
            return false;
        }

        return true;
    }

    private function emailValidation(?string $email): bool
    {
        if ($email === null) {
            $this->outputResult(err: 'no email provided');
            return false;
        }
        if (!(preg_match("/[0-9a-z]+@[a-z]*.*/", strtolower($email)))) {
            $this->outputResult(err: 'invalid email email');
            return false;
        }
        $queryResult = User::where('email', $email)->get();
        if (!empty($queryResult[0]->id)) {
            $this->outputResult(err: 'email already exists');
            return false;
        }
        return true;
    }

    public function checkAge($birthDate): bool
    {
        if ($birthDate <= 0) {
            $this->outputResult(err: 'birth date ' . $birthDate . ' is not allowed');
            return false;
        }

        return true;
    }


    public function get(Request $req, string $id = null)
    {
        if ($id !== null || gettype($id) !== 'integer') {
            return $this->checkZeroArray((User::where('id', $id)->get()));

        } elseif ($req->query('login') !== null) {
            return $this->checkZeroArray(User::where('name', $req->query('login'))->get());
            return;
        } else {
            $this->outputResult(err: 'no id nor login provided');
        }
    }

    public function authenticate(Request $req)
    {
        if ($req->json('login') === null) {
            $this->outputResult(err: 'no login provided');
            return;
        } elseif ($req->json('password') === null) {
            $this->outputResult(err: 'no password provided');
            return;
        }
        $hash = User::where('name', $req->json('login'))->get('password');
        if (Hash::check($req->json('password'), $hash[0]->password)
        ) {
            $this->outputResult(ok: 'success');
            return;
        } else {
            $this->outputResult(err: 'wrong login or password');
        }
    }

    public function delete(Request $req, string $id = null)
    {
        if ($id === null) {
            $this->outputResult(err: 'no id provided');
            return;
        }
        if (User::find($id)->delete() !== 0) {
            $this->outputResult(ok: 'Успех!');
            return;
        } else {
            $this->outputResult(err: 'Пользователя с таким id не существует');
            return;
        }
    }

    public function update(Request $req, string $id = null)
    {
        if ($id === null) {
            $this->outputResult(err: 'no id provided');
            return;
        }
        if (empty(User::where('id', $id)->get()[0])) {
            $this->outputResult(err: 'no such user');
            return;
        }
        if (
            $req->json('login') === null
            && $req->json('password') === null
            && $req->json('email') === null
            && $req->json('birth_date') === null
        ) {
            $this->outputResult(err: 'nothing to change');
            return;
        }

        if (
            $req->json('login') !== null
            && !empty(User::where('name', $req->json('login'))->get()[0])
        ) {
            $this->outputResult(err: 'login already in use');
            return;
        }

        if (
            $req->json('email') !== null
            && !empty(User::where('email', $req->json('email')))
        ) {
            if (!$this->emailValidation($req->json('email'))) {
                return;
            }

            User::where('id', $id)->update(['email' => $req->json('email')]);
        }

        if (
            $req->json('birth_date') !== null
        ) {
            if (!$this->emailValidation($req->json('birth_date'))) {
                return;
            }

            User::where('id', $id)->update(['birth_date' => $req->json('birth_date')]);
        }

        if ($req->json('password') !== null) {
            if (!$this->checkPassword($req->json('password'))) {
                return;
            }

            User::where('id', $id)->update(['password' => Hash::make($req->json('password'))]);
        }

        if ($req->json('login') !== null) {
            User::where('id', $id)->update(['name' => $req->json('login')]);
        }

        $this->outputResult(User::where('id', $id)->get()[0]);
    }

    public function register(Request $req)
    {
        if ($req->json('login') === null) {
            $this->outputResult(err: 'no login provided');
            return;
        }

        if (!$this->checkPassword($req->json('password'))) {
            return;
        }

        if (!$this->emailValidation($req->json('email'))) {
            return;
        }

        if (!$this->checkAge($req->json('birth_date'))) {
            return;
        }

        if (!empty(User::where('name', $req->json('login'))->get()[0])) {
            $this->outputResult(err: 'login already exists');
            return;
        }

        if (!empty(User::where('email', $req->json('email'))->get()[0])) {
            $this->outputResult(err: 'email already in use');
            return;
        }

        User::create([
            'name' => $req->json('login'),
            'email' => $req->json('email'),
            'password' => $req->json('password'),
            'birth_date' => $req->json('birth_date'),
            'male' => $req->json('male')
        ]);

        $this->outputResult(User::where('name', $req->json('login'))->get()[0]);
    }
}
