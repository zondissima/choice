<?php
require_once './init.php';

if (isset($_COOKIE['k'])) {
    //header('Location: board.php');
    //die();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = [];
    $data['set'] = $_GET['set'] ?? 'fruits';
    $data['set'] = preg_replace("/[^A-Za-z0-9]/", '', $data['set']);
    if (!is_dir(__DIR__ . '/assets/' . $data['set'])) {
        die('error');
    }

    $data['id'] = substr(md5(time()), 0, 6);
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    $data['deleted'] = [];

    $files = glob(__DIR__ . '/assets/' . $data['set'] . '/*.*');
    $count = count($files);

    // Random list of positions
    $positions = [];

    for ($i = 1; $i <= $count; $i++) {
        $positions[] = $i;
    }

    shuffle($positions);
    $data['positions'] = array_slice($positions, 0, 12);

    $data['next'] = 2;

    save_data($data);

    setcookie('k', $data['id'] . '-1', time() + 365 * 24 * 3600);

    header('Location: board.php?k=' . $data['id'] . '-1');
    die();
}

?><!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>The Choice</title>
        <meta name="description" content="Get always the best">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css">

    </head>
    <body>


        <div class="container" style="text-align: center">

            <h1>The Choice</h1>
            <h3>Get always the best</h3>
            <p>Every time you play different options are shown</p>
            <!--
            <h3>Find the most<br><br>
                🔥 💦 🍆 🍑<br><br>
                position for you and your lover</h3>
            <p>Every time you play different positions are shown!</p>
            -->
        </div>


        <div class="row">
            <div class="col-md-12" style="text-align: center;">
                <form action="" method="post">
                    <button class="btn btn-success btn-lg">Let's start!</button>
                </form>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    </body>
</html>
