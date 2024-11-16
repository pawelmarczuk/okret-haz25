<?php
function sortData($data)
{
    $multi = [];
    // $data = getDataRaw()->values;

    $point_array = array();
    $name_array = array();
    $level_array = array();
    $rate_array = array();
    foreach ($data as $key => $value) {
        $name = "";
        if (isset($value[0])) {
            $name = $value[0];
        }
        $point = "0";
        if (isset($value[1])) {
            $point = $value[1];
        }
        $level = "0";
        if (isset($value[2])) {
            $level = $value[2];
        }
        $rate = "0";
        if (isset($value[2])) {
            $rate = $value[3];
        }

        $point_array[$key] = $point;
        $name_array[$key] = $name;
        $level_array[$key] = $level;
        $rate_array[$key] = $rate;
    }
    array_multisort($point_array, SORT_DESC, SORT_NUMERIC, $name_array, $level_array, $rate_array);
    $multi['point_array'] = $point_array;
    $multi['name_array'] = $name_array;
    $multi['level_array'] = $level_array;
    $multi['rate_array'] = $rate_array;

    $output = [];
    foreach ($point_array as $key => $value) {
        $output[$key] = [
            0 => $name_array[$key],
            1 => $value,
            2 => $level_array[$key],
            3 => $rate_array[$key],
        ];
    }

    return $output;
}

function getSheepType($_point)
{
    // $point = floatval(str_replace(",", ".", $_point));
    // if ($point <= 0) {
    //     return 1;
    // } else if ($point <= 1) {
    //     return 2;
    // } else if ($point <= 2) {
    //     return 3;
    // } else {
    return 9;
    // }
}

$dataSorted = sortData($data);

?>
<html>

<head>
    <title>HAL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
        integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <style>
        #rank2 {
            position: fixed;
            bottom: 0;
            right: 0;
            border-color: black;
            border-style: solid;
            border-width: 1px;
            height: 200px;
            width: 200px;
            overflow-y: scroll;
        }

        .ship {
            position: absolute;
            width: 20px;
            width: 20px;
            background: green;
        }

        .ship-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 30px;
            height: 30px;
        }

        .ship-name {
            white-space: nowrap;
            border-color: black;
            border-style: solid;
            border-width: 1px;
            background-color: white;
            position: absolute;
            top: 6;
            right: 20;
            font-size: 7pt;
            padding: 2px;
        }

        .row-flex {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            width: 100%;
        }

        .column {
            display: flex;
            flex-direction: column;
        }

        #rank {
            width: 300px;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            border-color: black;
            border-style: solid;
            border-width: 1px;
        }

        .buoy {
            background-image: url("buoy.png");
            height: 100%;
            width: 20px;
            background-repeat: repeat-y;
        }

        .islands {
            background-image: url("islands.png");
            height: 100%;
            width: 30px;
            background-repeat: repeat-y;
        }


        body {
            font-size: 9pt;
            /* background-color: blue; */
            background-image: url("background4.jpg");

            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        table tr td,
        table tr th {
            background-color: rgba(210, 130, 240, 0.0) !important;
        }

        #bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Full height */
            height: 100%;
            /* The image used */

        }

        .arrow {
            width: 110px;
        }

        .line {
            margin-top: 7px;
            width: 95px;
            background: blue;
            height: 5px;
            float: left;
        }

        .point {
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 15px solid blue;
            float: right;
        }
    </style>
    <script>
        var widthShip = 25;
        var intervalTime = 100;
        var step = 1;
        var ship = [];
        var maxWidht = 0;
        var changeDirectionAfterSteps = 100;

        function init() {
            maxWidht = $("#track").width() - widthShip;
            ship = [
                <?php for ($i = 0; $i < count($data); $i++): ?>[25, <?= $i * 25 ?>, <?= (isset($data[$i][2]) ? ($data[$i][2] / 12) : 0) ?>, 0, 0, <?= rand(-1, 1) ?>, <?= (isset($data[$i][1])) ? getSheepType($data[$i][3]) : 0; ?>],
                <?php endfor; ?>
            ];

            var left = 0;
            var interval = setInterval(function() {
                var moveSheeps = false;
                for (var i = 0; i < ship.length; i++) {
                    if (moveSheep(i, ship[i])) {
                        moveSheeps = true;
                    }
                }
                if (moveSheeps == false) {
                    clearInterval(interval);
                }
            }, intervalTime);
        }

        function moveSheep(number, sheepData) {
            // 0 - x
            // 1 - y
            // 2 - maxX;
            // 3 - rotate;
            // 4 - change direction
            // 5 - mode (0 - wprost, 1 do góry, -1 do dołu)
            // 6 - velocity 

            var velocity = sheepData[6]
            var radians = (Math.PI / 180) * sheepData[3];
            sheepData[0] += Math.cos(radians) * velocity;
            sheepData[1] += Math.sin(radians) * velocity;
            if (sheepData[1] < 0) {
                sheepData[1] = 0;
                sheepData[3] = 0;
            }


            if (sheepData[0] > maxWidht * sheepData[2]) {
                $("#ship" + number).css({
                    'transform': 'rotate(0deg)'
                });
                return false;
            }
            sheepData[4] += 1;

            if (sheepData[4] >= changeDirectionAfterSteps) {
                if (getRandomInt(20) > 15) {
                    sheepData[4] = 0;
                    sheepData[5] = getNewMode(sheepData[5]);
                }
            }
            // if (sheepData[5] == MODE_UP && sheepData[3] < 1) {
            //     sheepData[3] += 1;
            // } else if (sheepData[5] == MODE_DOWN && sheepData[3] > -1) {
            //     sheepData[3] -= 1;
            // } else if (sheepData[5] == MODE_STRAIGHT && sheepData[3] < 0) {
            //     sheepData[3] += 1;
            // } else if (sheepData[5] == MODE_STRAIGHT && sheepData[3] > 0) {
            //     sheepData[3] -= 1;
            // }

            var rotation = sheepData[3];

            $("#ship" + number).css({
                left: sheepData[0] + "px",
                top: sheepData[1] + "px",
                'transform': 'rotate(' + rotation + 'deg)'
            });
            return true;
        }

        function getRotation() {
            var rand = getRandomInt(3);
            switch (rand) {
                case 0:
                    return 1;
                    break;
                case 1:
                    return -1;
                    break;
                default:
                    return 0;
                    break;
            }
        }

        var MODE_UP = 1;
        var MODE_STRAIGHT = 0;
        var MODE_DOWN = -1;

        function getNewMode(mode) {
            var rand = getRandomInt(2);
            switch (mode) {
                case MODE_UP:
                    return (rand == 0) ? MODE_UP : MODE_STRAIGHT;
                    break;
                case MODE_DOWN:
                    return (rand == 0) ? MODE_DOWN : MODE_STRAIGHT;
                    break;
                default:
                    return (rand == 0) ? MODE_UP : MODE_DOWN;
                    braek;
            }
        }

        function getRandomInt(max) {
            return Math.floor(Math.random() * max);
        }

        $(document).ready(function() {
            init();
        });
    </script>
</head>

<body>
    <div id="bg"></div>
    <div class='row-flex'>
        <div class='column' style="flex: 1;position:relative;">
            <div class='row-flex'>
                <div class="arrow">
                    <div class="line"></div>
                    <div class="point"></div>
                </div>
                <div style="color:blue;padding:3px;"><b>Poziom przygotowań do wyjazdu.</b> Im więcej zrealizowanych zadań, tym dalej jedzie narciarz.</div>

            </div>
            <div id="track" style="position:relative;height:100%;">
                <div class="buoy" style="position:absolute;left:6%;"></div>
                <div class="buoy" style="position:absolute;left:12%;"></div>
                <div class="buoy" style="position:absolute;left:20%;"></div>
                <div class="buoy" style="position:absolute;left:28%;"></div>
                <div class="buoy" style="position:absolute;left:36%;"></div>
                <div class="buoy" style="position:absolute;left:44%;"></div>
                <div class="buoy" style="position:absolute;left:52%;"></div>
                <div class="buoy" style="position:absolute;left:60%;"></div>
                <div class="islands" style="position:absolute;left:68%;"></div>
                <div class="buoy" style="position:absolute;left:76%;"></div>
                <div class="buoy" style="position:absolute;left:84%;"></div>
                <div class="buoy" style="position:absolute;left:92%;"></div>

                <?php $place = -1; ?>
                <?php foreach ($data as $key => $value): ?>
                    <?php $id = (++$place) ?>
                    <div class="ship" id="ship<?= $id ?>" style="top:<?= ($id * 25) ?>px;left:20px;">
                        <div class="ship-name">
                            <?= (isset($value[0])) ? $value[0] : "" ?>
                        </div>

                        <?php
                        $type = 1;
                        if (isset($value[1])) {
                            $type = getSheepType($value[3]);
                        }
                        switch ($type):
                            default: ?>
                                <img src="skier.gif" class="ship-img" />
                        <?php break;

                        endswitch;
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        <div id='rank' class="column">
            <div class="row-flex" style="align-items: center;font-size:7pt;">
                <div class="column" style="flex: 1;text-align:center;">
                    <h3>Ranking</h3>
                    <table class="table">
                        <thead>
                            <th></th>
                            <th style="font-size:9pt;">wyjazd</th>
                            <th style="font-size:9pt;">okręgoleony</th>
                        </thead>
                        <tbody>
                            <?php

                            $place = 0;
                            $savePlace = 1;
                            $saveValue = -1; ?>
                            <?php foreach ($dataSorted as $key => $value): ?>
                                <?php
                                if ($saveValue != $value[1]) {
                                    $place++;
                                }
                                ?>
                                <tr>
                                    <td style="font-size:8pt;">
                                        <?= $place ?>
                                    </td>
                                    <td style="font-size:8pt;">
                                        <?= $value[0] ?>
                                    </td>
                                    <td style="font-size:8pt;">
                                        <?= $value[1] ?>
                                    </td>
                                </tr>
                                <?php
                                $saveValue = $value[1]
                                ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>