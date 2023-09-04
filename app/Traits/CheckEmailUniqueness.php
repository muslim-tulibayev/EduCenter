<?php

namespace App\Traits;

use App\Models\Stparent;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

trait CheckEmailUniqueness
{
    public function checkForEmailUniqueness($email): bool
    {
        $found = User::where('email', $email)->first();
        if (!$found)
            $found = Teacher::where('email', $email)->first();
        if (!$found)
            $found = Stparent::where('email', $email)->first();
        if (!$found)
            $found = Student::where('email', $email)->first();

        if ($found)
            return false;
        else
            return true;
    }
}
