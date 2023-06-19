<?php
function getUserData($userData)
{
    config(['app.user_id' => $userData]);
    // return  $userData;
}

function getUserData1()
{
    //return "hai";
    return config('app.user_id');
}
