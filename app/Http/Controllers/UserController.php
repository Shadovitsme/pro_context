<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            ];
        }

        if ($err) {
            $result = array_merge(['error' => $err], $result);
        }

        if ($ok) {
            $result = array_merge(['ok' => $ok], $result);
        }

        echo (json_encode($result));
        return;
    }
    public function checkZeroArray($queryResult)
    {
        if (!empty($queryResult->id)) {
            $this->outputResult($queryResult);
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

    public function get(Request $req, string $id = null)
    {

        if ($id !== null) {
            $this->checkZeroArray(User::find($id));

        } elseif ($req->query('login') !== null) {
            $this->checkZeroArray(User::where('name', $req->query('login'))->get());
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

        if ($req->json('password') !== null) {
            if (!$this->checkPassword($req->json('password'))) {
                return;
            }

            User::where('id', $id)->update(['password' => $req->json('password')]);
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

        if (!empty(User::where('name', $req->json('login'))->get()[0])) {
            $this->outputResult(err: 'login already exists');
            return;
        }

        User::create([
            'name' => $req->json('login'),
            'email' => $req->json('login') . rand(10, 100) . '@mail.ru',
            'password' => $req->json('password'),
            'age' => 10
        ]);

        $this->outputResult(User::where('name', $req->json('login'))->get()[0]);
    }
}
