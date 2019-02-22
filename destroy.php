<?php

    /* For testing purposes, kill the session and start fresh */

    session_start();
    session_destroy();
    var_dump($SESSION);