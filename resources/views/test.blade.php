<?php

use App\Models\User;

User::create(['name' => 'testuser', 'email' => 'test', 'password' => 'workpls']);
foreach (User::all() as $user) {
    echo $user->name;
}
