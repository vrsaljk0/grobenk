<script>
    function myFunction() {
        var objDiv = document.getElementById("sve");
        objDiv.scrollTop = objDiv.scrollHeight;
    }
</script>


<?php
require_once "dbconnect.php";
require_once "functions.php";
session_start();
mysqli_set_charset($conn,"utf8");
/** SESSION TIMEOUT */
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    header("Location:odjava.php");
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['donor_loggedin'])) header("Location:denied_permission.php");

echo '
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BloodBank</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
    <link href="style.css" rel="stylesheet">
    <link href="donorstyle.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">
    <link href="tmp.css" rel="stylesheet">
</head>


</head>
<body>';
echo "
    <div id='nav-placeholder' onload>
    </div> 

    <script>
    $(function(){
      $('#nav-placeholder').load('donornavbar.php');
    });
    </script>";

$OIB = $_SESSION['id'];
$datum = date('Y-m-d H:i:s');

$info = "SELECT *from donor where OIB_donora = '$OIB'";
$run = mysqli_query($conn, $info);
$result = $run or die ("Failed to query database". mysqli_error($conn));
$row = mysqli_fetch_array($result);
$moje_ime = $row['ime_prezime_donora'];
$username = $_GET['username']; //username od donora na kojeg sam ušla
$moja_image = $row['image'];
//echo $username;

$prijatelj = "SELECT *from donor where username = '$username'";
$run2 = mysqli_query($conn, $prijatelj);
$result2 = $run2 or die ("Failed to query database". mysqli_error($conn));
$row2 = mysqli_fetch_array($result2);
$OIB_frenda = $row2['OIB_donora'];
$ime_frenda = $row2['ime_prezime_donora'];
$frend_image = $row2['image'];

$sql_zadnja_admin = "SELECT * from obavijesti WHERE OIBdonora='$OIB' and ID_posiljatelja ='1' order by datum_obav DESC LIMIT 1";
$run_zadnja_admin = mysqli_query($conn, $sql_zadnja_admin);
$result_zadnja_admin =  $run_zadnja_admin or die ("Failed to query database". mysqli_error($conn));
$row_zadnja_admin = mysqli_fetch_array($result_zadnja_admin);
if(mysqli_num_rows($result_zadnja_admin) == 0){
    $zadnji_datum_admin = "";
    $zadnja_poruka_admin = "Trenutno nema poruka";
    $stanje_admin = "1";
}
else{
    $zadnji_datum_admin = $row_zadnja_admin['datum_obav'];
    $zadnja_poruka_admin = $row_zadnja_admin['tekst_obav'];
    $stanje_admin = $row_zadnja_admin['procitano'];
}

if(isset($_POST['posalji_poruku'])){
    $tekst = $_POST['poruka'];
    if($tekst!=''){
        $message = "INSERT INTO obavijesti (OIBdonora, ID_posiljatelja, tekst_obav, datum_obav, procitano) VALUES ('$OIB_frenda', '$OIB', '$tekst', '$datum', '0')";
        $run2 = mysqli_query($conn, $message);
        $result2 = $run or die ("Failed to query database". mysqli_error($conn));

    }
    $url = 'user_history.php?username='.$username;
    header("Location:$url");
}
$sql_korisnici = "SELECT * from obavijesti where OIBdonora ='$OIB' and ID_posiljatelja!='1' group by ID_posiljatelja order by datum_obav";
$run_korisnici = mysqli_query($conn, $sql_korisnici);
$result_korisnici = $run_korisnici or die ("Failed to query database". mysqli_error($conn));

$d = $zadnji_datum_admin;
$day = date("d", strtotime($d));
$month = date("m", strtotime($d));
$year = date("Y", strtotime($d));

if($month == 1) $mjesec = "Siječanj";
if($month == 2) $mjesec = "Veljača";
if($month == 3) $mjesec = "Ožujak";
if($month == 4) $mjesec = "Travanj";
if($month == 5) $mjesec = "Svibanj";
if($month == 6) $mjesec = "Lipanj";
if($month == 7) $mjesec = "Srpanj";
if($month == 8) $mjesec = "Kolovoz";
if($month == 9) $mjesec = "Rujan";
if($month == 10) $mjesec = "Listopad";
if($month == 11) $mjesec = "Studeni";
if($month == 12) $mjesec = "Prosinac";

if($stanje_admin == 0) {
echo '
<div class="container" id="sve" onload="myFunction();">
<div class="messaging">
      <div class="inbox_msg">
        <div class="inbox_people">
          <div class="headind_srch">
            <div class="recent_heading">
              <h4 style="color:#9F0A00;">Povijest poruka</h4>
            </div>
            <div class="srch_bar">
              <div class="stylish-input-group">
                <form action="user_search.php" method="POST">
                    <input type="text" class="search-bar" name="search_uvjet"  placeholder="Pretraži" >
                    <span class="input-group-addon">
                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </form>
                </span> </div>
            </div>
          </div>
          <div class="inbox_chat">
            <div class="chat_list">
                <a href="admin_history.php">
                <div style="background-color:#FFD3D3" class="chat_people">
                    <div class="chat_img"> <img src="donori/admin.png"> </div>
                    <div class="chat_ib">
                      <h5>Admin <span class="chat_date">'.$day.'. '.$mjesec.' '.$year.'.</span></h5>
                      <p style="color:black;">'.$zadnja_poruka_admin.'</p>

                    </div>
                </div>
                </a><br>';

}

else {

echo '
<div class="container" id="sve" onload="myFunction();">
<div class="messaging">
      <div class="inbox_msg">
        <div class="inbox_people">
          <div class="headind_srch">
            <div class="recent_heading">
              <h4 style="color:#9F0A00;">Povijest poruka</h4>
            </div>
            <div class="srch_bar">
              <div class="stylish-input-group">
                <form action="user_search.php" method="POST">
                    <input type="text" class="search-bar" name="search_uvjet"  placeholder="Pretraži" >
                    <span class="input-group-addon">
                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </form>
                </span> </div>
            </div>
          </div>
          <div class="inbox_chat">
            <div class="chat_list">
                <a href="admin_history.php">
                <div class="chat_people">
                    <div class="chat_img"> <img src="donori/admin.png"> </div>
                    <div class="chat_ib">
                      <h5>Admin <span class="chat_date">'.$day.'. '.$mjesec.' '.$year.'.</span></h5>
                      <p>'.$zadnja_poruka_admin.'</p>

                    </div>
                </div>
                </a><br>';
}


while($row = mysqli_fetch_array($result_korisnici)){
    $OIB_prijatelja = $row['ID_posiljatelja'];
    $prijatelj = "SELECT * from donor where OIB_donora = '$OIB_prijatelja'";
    $run_prijatelj = mysqli_query($conn, $prijatelj);
    $result2 = $run_prijatelj or die ("Failed to query database". mysqli_error($conn));
    $row_prijatelj = mysqli_fetch_array($result2);
    $ime = $row_prijatelj['ime_prezime_donora'];
    $username_prijatelja = $row_prijatelj['username'];

    $sql_zadnja = "SELECT * from obavijesti WHERE (OIBdonora = '$OIB' AND ID_posiljatelja = '$OIB_prijatelja') OR (OIBdonora = '$OIB_prijatelja' AND ID_posiljatelja ='$OIB') order by datum_obav DESC LIMIT 1";
    $run_zadnja = mysqli_query($conn, $sql_zadnja);
    $result_zadnja = $run_zadnja or die ("Failed to query database". mysqli_error($conn));
    $row_zadnja = mysqli_fetch_array($result_zadnja);
    $d = $row_zadnja['datum_obav'];
    $stanje = $row_zadnja['procitano'];
    $ID = $row_zadnja['ID_posiljatelja'];
    if($username_prijatelja == $username){
        $id_zadnje_por = $row_zadnja['id_obavijesti'];
        if($ID != $OIB){
            $sql_update_zadnju = "UPDATE obavijesti SET procitano = '1' WHERE id_obavijesti = '$id_zadnje_por'";
            $run_update = mysqli_query($conn, $sql_update_zadnju);
            $result_update = $run_update or die ("Failed to query database". mysqli_error($conn));
        }
         //ne zelimo da nam se označi naša poruka crvenim
    }
    if($ID == $OIB) $stanje = 1;
    $day = date("d", strtotime($d));
    $month = date("m", strtotime($d));
    $year = date("Y", strtotime($d));

    if($month == 1) $mjesec = "Siječanj";
    if($month == 2) $mjesec = "Veljača";
    if($month == 3) $mjesec = "Ožujak";
    if($month == 4) $mjesec = "Travanj";
    if($month == 5) $mjesec = "Svibanj";
    if($month == 6) $mjesec = "Lipanj";
    if($month == 7) $mjesec = "Srpanj";
    if($month == 8) $mjesec = "Kolovoz";
    if($month == 9) $mjesec = "Rujan";
    if($month == 10) $mjesec = "Listopad";
    if($month == 11) $mjesec = "Studeni";
    if($month == 12) $mjesec = "Prosinac";

     if($username_prijatelja == $username) {
        echo '
        <a class="a" href="user_history.php?username='.urlencode($username_prijatelja).'">
            <div style="border-style:solid; border-color:black; border-width: thin; background:#ebebeb;" class="chat_people">
                <div class="chat_img"> <img style="width:50px; height:50px; object-fit:cover;" src="donori/'.$row_prijatelj['image'].'"> </div>
                <div class="chat_ib">
                  <h5>'.$row_prijatelj['ime_prezime_donora'].'<span class="chat_date">'.$day.'. '.$mjesec.' '.$year.'.</span></h5>
                  <p>'.$row_zadnja['tekst_obav'].'</p>
                </div>
            </div>
        </a><br>
        ';
     }

     else if ($stanje == 0) {
        echo '
            <a class="a" href="user_history.php?username='.urlencode($username_prijatelja).'">
                <div style="background-color:#FFD3D3" class="chat_people">
                    <div class="chat_img"> <img src="donori/'.$row_prijatelj['image'].'"> </div>
                    <div class="chat_ib">
                      <h5>'.$row_prijatelj['ime_prezime_donora'].'<span class="chat_date">'.$day.'. '.$mjesec.' '.$year.'.</span></h5>
                      <p style="color:black;">'.$row_zadnja['tekst_obav'].'</p>
                    </div>
                </div>
            </a><br>';
     }

     else {

        echo '
            <a class="a" href="user_history.php?username='.urlencode($username_prijatelja).'">
                <div class="chat_people">
                    <div class="chat_img"> <img style="width:50px; height:50px; object-fit:cover;" src="donori/'.$row_prijatelj['image'].'"> </div>
                    <div class="chat_ib">
                      <h5>'.$row_prijatelj['ime_prezime_donora'].'<span class="chat_date">'.$day.'. '.$mjesec.' '.$year.'.</span></h5>
                      <p>'.$row_zadnja['tekst_obav'].'</p>
                    </div>
                </div>
            </a><br>';
    }
}

echo '
            </div>
          </div>
        </div>

        <div class="mesgs">
            <div class="msg_history">';

$poruke = "SELECT * from obavijesti WHERE (OIBdonora = '$OIB' AND ID_posiljatelja = '$OIB_frenda') OR (OIBdonora = '$OIB_frenda' AND ID_posiljatelja ='$OIB')";
$run_poruke = mysqli_query($conn, $poruke);
$result_poruke = $run_poruke or die ("Failed to query database". mysqli_error($conn));

while($row_poruke = mysqli_fetch_array($result_poruke)){
    $dat = $row_poruke['datum_obav']; 
    $hour = date("H", strtotime($dat));
    $min = date("i", strtotime($dat));
    $day = date("d", strtotime($dat));
    $month = date("m", strtotime($dat));
    $year = date("Y", strtotime($dat));
    if($row_poruke['ID_posiljatelja'] == $OIB){
        echo '
                <div class="outgoing_msg">
                  <div class="sent_msg">
                    <p style="background-color:#9F0A00;">'.$row_poruke['tekst_obav'].'</p>
                    <span class="time_date"> '.$hour.':'.$min.' '.$day.'.'.$month.'.'.$year.'.</span> </div>
                </div>
                    ';
    }
    else echo '
                <div class="incoming_msg">
                    <div class="incoming_msg_img"> <img src="donori/'.$frend_image.'" alt="sunil"> </div>
                        <div class="received_msg">
                        <div class="received_withd_msg">
                        <p>'.$row_poruke['tekst_obav'].'</p>
                        <span class="time_date"> '.$hour.':'.$min.' '.$day.'.'.$month.'.'.$year.'.</span></div>
                    </div>
                </div>
                ';
}



echo '
          </div>
          <div class="type_msg">
            <div class="input_msg_write">
                <form action="" method="POST">
                  <input type="text" class="write_msgin" placeholder="Napiši poruku" name="poruka"/>
                  <input style="background-color:#9F0A00;" class="msg_send_btn" type="submit" name="posalji_poruku" value="&#10148">
                </form>
           </div>
          </div>
        </div>
      </div>
    </div></div>
    </body>
    </html>';
?>