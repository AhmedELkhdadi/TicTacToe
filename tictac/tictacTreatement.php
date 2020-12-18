<? include_once "connexion.php";
$conn = connectdb();
session_start();
if(isset($_POST['code'])){
    $id_sess=$_POST["code"];
    $_SESSION['game']= $id_sess;
    mysqli_query($conn,"INSERT INTO round(round_ID) values('$id_sess');");
}
if(isset($_REQUEST['sess'] )){
$codeSess = $_REQUEST['sess'] ;
$res = mysqli_query($conn, "SELECT player2 FROM round WHERE round_ID = '$codeSess'");
echo mysqli_fetch_assoc($res)['player2'];
}



if(isset($_POST['codeEntered'])){
    $entered = $_POST['codeEntered'];
    if($entered ==""){
        echo "Please enter a code.";
        return;
    }
    
    $res = mysqli_query($conn,"SELECT player2 FROM round WHERE round_ID = '$entered'");
    
    if (mysqli_num_rows($res)==0) {
        echo "Session not found, try another code";
        return;
    }
    elseif(mysqli_fetch_assoc($res)['player2']==1){
        echo "Session full, try another code";
        return;
    }
    
    $sql = "UPDATE round SET player2 = 1 WHERE round_ID = '$entered' and player2 = 0";
    if(mysqli_query($conn,$sql)){
        $_SESSION['game']= $entered;
        echo "Joined game succesfully.";
        return;
    }
    else{
        echo "A probleme occured, try with another code.";
        return;
    }
}
if(isset($_POST['round']))
{
    $game_ID = $_SESSION['game'];
    $played = $_POST['played'];
    $sql = "UPDATE round SET lastChanged = $played WHERE round_ID = '$game_ID'";
    mysqli_query($conn,$sql);
}
if(isset($_REQUEST['game']))
{
    $game_ID = $_SESSION['game'];
    $res = mysqli_query($conn,"SELECT lastChanged FROM round WHERE round_ID = '$game_ID'");
    echo mysqli_fetch_assoc($res)['lastChanged'];
}
if(isset($_POST['player'])){
    $game_ID = $_SESSION['game'];
    if($_POST['player']==1)
    {
        $sql = "UPDATE round SET player1 = 1 WHERE round_ID = '$game_ID'";
    }
    if($_POST['player']==2)
    { 
        $sql = "UPDATE round SET player2 = 1 WHERE round_ID = '$game_ID'";
    }
    mysqli_query($conn,$sql);
}
if(isset($_REQUEST['cont'])){
    $codegame =$_SESSION['game'];
    $sql = "SELECT * FROM round WHERE round_ID = '$codegame'";
    $res = mysqli_query($conn,$sql);
    $row = mysqli_fetch_assoc($res);
    if (mysqli_num_rows($res) == 0)
       { echo 0;}
    else
    if($row['player1']==1 && $row['player2']==1)
        echo 11;
    else
        echo 2;
}
if(isset($_POST['gamecoode'])){
    $game_ID = $_SESSION['game'];
    echo mysqli_query($conn,"UPDATE round SET player1 = 0 , player2 = 0 , lastChanged = null WHERE round_ID = '$game_ID'");
}
if(isset($_POST['coode'])){
    $game_ID = $_SESSION['game'];
    mysqli_query($conn,"DELETE FROM round WHERE round_ID = '$game_ID'");
}