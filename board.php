<?php
require_once './init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['restart'])) {
        setcookie('k', '', 0);
        header('Location: index.php');
        die();
    }
}

if (isset($_GET['k'])) {
    $key = $_GET['k'];
    setcookie('k', $key, time() + 365 * 24 * 3600);
} else if (isset($_COOKIE['k'])) {
    $key = $_COOKIE['k'];
} else {
    header('Location: index.php');
    die();
}

list($id, $who) = explode('-', $key, 2);

$data = get_data($id);

if (!$data) {
    setcookie('k', '', 0);
    header('Location: index.php');
    die();
}

$next = $data['next'];

$action = $_GET['action'] ?? '';

if ($action === 'check-reload' || $action === 'reload') {
    $response = ['reload' => $next == $who || isset($data['last']), 'data' => $data, 'who' => $who];
    header('Content-Type: application/json;charset=UTF-8');
    echo json_encode($response, JSON_PRETTY_PRINT);
    die();
}

// AJAX
if (isset($_GET['delete'])) {

    $position = $_GET['delete'];
    if (!in_array($position, $data['deleted'])) {
        $data['deleted'][] = $position;
    }
    if (count($data['deleted']) === count($data['positions']) - 1 && !isset($data['last'])) {
        $temp = array_diff($data['positions'], $data['deleted']);
        // Mantiene l'indice come chiave, quindi $temp[0] non funziona
        $data['last'] = array_shift($temp);
        save_data($data);
        die();
    }

    $data['next'] = $data['next'] == 2 ? 1 : 2;
    save_data($data);
    die();
}

$is_last = isset($data['last']);
$is_first = empty($data['deleted']);
?><!DOCTYPE html>
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
        <style>
            .posizione {
                cursor: pointer;
            }
        </style>
    </head>
    <body>


        <div class="container pb-2">
            <div class="row">
                <div class="col-md-12" style="text-align: center">
                    <? if ($is_last) { ?>
                        <h1>
                            The choice is made!
                        </h1>
                        <script type="text/javascript">
                            //let audio = new Audio('assets/end.mp3?ver=1');
                            //audio.play();
                        </script>
                    <? } else { ?>
                        <? if ($who != $next) { ?>
                            <? if ($is_first) { ?>
                                <h1>
                                    It's time to invite your playmate!<br>
                                    💌
                                </h1>
                                <h5>Share this link:</h5>
                                <h4><?= ROOT_URL ?>/board.php?k=<?= $data['id'] ?>-2</h4>
                                <br>
                                <h4>
                                    <a href="whatsapp://send?text=Play with me: <?= ROOT_URL ?>/board.php?k=<?= $data['id'] ?>-2"><i class="fa fa-whatsapp"></i> Send via Whatspp</a>
                                </h4>
                                <br><br><br>
                                <h5>
                                    And wait the playmate move
                                </h5>
                            <? } else { ?>
                                <h1>
                                    Ok, you're now waiting your playmate move
                                </h1>
                            <? } ?>

                        <? } else { ?>
                            <? if ($is_first) { ?>
                                <h1>
                                    Hi, here the game starts!
                                </h1>
                                <h2>
                                    <!--Don't be shy, it's just a game: click or tap a K position you <u>DO NOT</u> like!-->
                                    Select the option you like LESS.
                                </h2>
                            <? } else { ?>
                                <h1>
                                    Hi, it's your turn again!
                                </h1>
                                <h2>
                                    Select the option you like LESS.
                                    <!--Click or tap the K position you <u>DO NOT</u> like!-->
                                </h2>
                            <? } ?>
                        <? } ?>
                    <? } ?>
                </div>
            </div>

            <br><br>


            <div class="row positions">

                <? if ($is_last) { ?>
                    <div class="col-md-12" style="text-align: center">
                        <img src="<?= get_image($data['last'], $data['set']) ?>">
                        <br><br>


                    </div>
                    <div class="col-md-12" style="text-align: center">
                        <h2>Your rank</h2>
                    </div>
                    <? foreach (array_reverse($data['deleted']) as $i) { ?>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <img src="<?= get_image($i, $data['set']) ?>" class="position">
                            <br><br>
                        </div>
                    <? } ?>
                    <div class="col-md-12" style="text-align: center">
                        <form method="post">
                            <h3>There are many other options to explore</h3>
                            <button name="restart" class="btn btn-success btn-lg">Play again!</button>
                        </form>
                    </div>

                <? } else { ?>

                    <? if (!$is_first || $next == $who) { ?>
                        <? foreach ($data['positions'] as $i) { ?>

                            <div class="col-md-3 col-sm-4 col-xs-6" style="position: relative">
                                <? if (in_array($i, $data['deleted'])) { ?>
                                    <img src="<?= get_image($i, $data['set']) ?>" id="k<?= $i ?>" style="filter: brightness(30%)">
                                <? } else { ?>
                                    <img src="<?= get_image($i, $data['set']) ?>" class="position" id="k<?= $i ?>">
                                <? } ?>
                                <br><br>

                            </div>
                        <? } ?>
                    <? } ?>
                <? } ?>
            </div>

        </div>


        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

        <script type="text/javascript">
<? if (!$is_last && $who == $next) { ?>
                                $(".position").click(function () {
                                    if (confirm("Do you want to remove that option?")) {
                                        scroll(0, 0);
                                        $.post("board.php?delete=" + this.id.substring(1), function () {
                                            location.reload();
                                        });
                                    }
                                });

                                let audio = new Audio('assets/notification.mp3?ver=1');
                                audio.play();

<? } else if (!$is_last) { ?>
                                window.setInterval(function () {
                                    $.get(location.href + "&action=reload", function (data) {
                                        if (data.reload) {
                                            location.reload();
                                        }
                                    });
                                }, 3000);
<? } ?>
        </script>
    </body>
</html>

