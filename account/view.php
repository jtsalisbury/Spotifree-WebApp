<?php 
    include("../res/util/helper.php");
    error_reporting(E_ERROR | E_PARSE);

    include_once "../res/util/enums.php";
    include_once "../res/util/jwt.php";

    if (!checkLogin()) {
        header("Location: ./login.php");
    }

    // Parse settings and initialize global Jobjects
    $data = parse_ini_file("../res/util/settings.ini");

    // Used for: creation, verification and decoding of tokens
    $jwt = new JWT($data["signKey"], $data["signAlgorithm"], $data["payloadSecret"], $data["payloadCipher"]);
  
    // Used for: global ENUMS
    $ENUMS = new ENUMS();

    $id = $_GET["id"];
    if(empty($id)){
        header("Location: ../listings/viewall.php");
    }

    $data = array("uid" => $id);

    $token = $jwt->generateToken($data);
    $token = json_encode(array("token" => $token));

    $url = "http://u747950311.hostingerapp.com/househub/api/user/retrieve.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    if ($result === false) {
        die("internal application error " . $result);
    }

    $result_d = json_decode($result, true);
    if ($result_d["status"] == "error") {
        die($result);
    }

    $token = $result_d["message"];
    if ($jwt->verifyToken($token) === false) {
        die("");
    }

    $payload = json_decode($jwt->decodePayload($token), true);
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="http://u747950311.hostingerapp.com/househub/site/res/css/bootstrap.min.css">
        <link rel="apple-touch-icon" sizes="57x57" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//favicon-16x16.png">
        <link rel="manifest" href="http://u747950311.hostingerapp.com/househub/site/res/img/icon//manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="http://u747950311.hostingerapp.com/househub/site/res/img/icon//ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <style>
            .card-horizontal {
                display: flex;
                flex: 1 1 auto;
            }

            .card-img {
                width: 14rem;
                height: 184.4px;
            }

            .text-muted {
                font-size: 20px;
            }
        </style>

        <title>HouseHub | View User</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="#">
                <img src="http://u747950311.hostingerapp.com/househub/site/res/img/hh-icon.png" height="35" width="35" alt="">
            </a>
            <a class="navbar-brand" href="#">HouseHub</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarColor01">
                            
                <ul class="navbar-nav ml-auto justify-content-end">
                  <li class="nav-item">
                    <a class="nav-link" href="../listings/viewall.php">View Listings</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="../listings/create.php">Post Listing</a>
                  </li>
                  <li class="nav-item btn-group">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">My Account</a>
                    
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item" href="./update.php">Update Information</a>
                      <a class="dropdown-item" href="./logout.php">Logout</a>
                    </div>
                  </li>
                </ul>
            </div>

        </nav>

       <div class="container-fluid" style="height:calc(100vh - 121.5px - 56px); margin-top:30px; margin-bottom:30px;">
           <div class="row">

                <div class="card offset-md-3 col-md-6 offset-sm-1 col-sm-10">
                    <div class="card-body">
                        <h5 class="card-title">User Information</h5>

                         <p>
                            Name: <? echo $payload["fname"]; ?>
                            <? echo $payload["lname"]; ?>
                            <br>
                            Email: <? echo $payload["email"]; ?>
                            <br>
                            Number of Listings: <? echo $payload["num_listings"]; ?>
                        </p>

                    </div>
                </div>
            </div>
        </div>
<footer class="page-footer font-small blue">
  <div class="footer-email text-center py-3"> Questions or Concerns? <a href="mailto:househubteam@gmail.com" target="_blank">Contact us!</a> 
  </div>
</footer>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="http://u747950311.hostingerapp.com/househub/site/res/js/main.js"></script>
        <script src="http://u747950311.hostingerapp.com/househub/site/res/js/viewListings.js"></script>
    </body>
</html>




          