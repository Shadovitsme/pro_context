<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $this->checkZeroArray(DB::table('users')->find($id));

        } elseif ($req->query('login') !== null) {
            $this->checkZeroArray(DB::table('users')->where('name', $req->query('login'))->get());
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
        if (
            !empty(DB::table('users')
                ->where('name', $req->json('login'))
                ->where('password', hash('sha256', $req->json('password')))
                ->get()[0])
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
        if (DB::table('users')->delete($id) !== 0) {
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
        if (empty(DB::table('users')->where('id', $id)->get()[0])) {
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
            && !empty(DB::table('users')->where('name', $req->json('login'))->get()[0])
        ) {
            $this->outputResult(err: 'login already in use');
            return;
        }

        if ($req->json('password') !== null) {
            if (!$this->checkPassword($req->json('password'))) {
                return;
            }

            DB::table('users')->where('id', $id)->update(['password' => hash('sha256', $req->json('password'))]);
        }

        if ($req->json('login') !== null) {
            DB::table('users')->where('id', $id)->update(['name' => $req->json('login')]);
        }

        $this->outputResult(DB::table('users')->where('id', $id)->get()[0]);
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

        if (!empty(DB::table('users')->where('name', $req->json('login'))->get()[0])) {
            $this->outputResult(err: 'login already exists');
            return;
        }

        DB::table('users')->insert([
            'name' => $req->json('login'),
            'email' => rand(10, 100) . '@mail.ru',
            'password' => hash('sha256', $req->json('password')),
        ]);

        $this->outputResult(DB::table('users')->where('name', $req->json('login'))->get()[0]);
    }
}
