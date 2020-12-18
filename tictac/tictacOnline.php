<? 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Document</title>
    <style>
        #main {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;

        }

        #tab {
            border-collapse: collapse;
            width: 500px;
            height: 500px;
            margin: auto;
            padding: 20px;

        }

        .X {
            color: brown;
        }

        .O {
            color: blue;
        }



        #tab td {
            border: 1px solid black;
            width: 33%;
            text-align: center;

            font-size: 80px;
        }

        #tab tr {
            height: 33%;
        }

        #tab tr:first-child td {
            border-top: 0;
        }

        #tab tr td:first-child {
            border-left: 0;
        }

        #tab tr td:last-child {
            border-right: 0;
        }

        #tab tr:last-child td {
            border-bottom: 0;
        }
    </style>
</head>

<body>
    <div id="main">
        <div id="gametable">
            <h1>TIC TAC TOE</h1>

            <h3 style="height: 15px;" id="winner"></h3>
            <table id="tab" style="visibility: hidden;">
                <tr>
                    <td id="0"></td>
                    <td id="1"></td>
                    <td id="2"></td>
                </tr>
                <tr>
                    <td id="3"></td>
                    <td id="4"></td>
                    <td id="5"></td>
                </tr>
                <tr>
                    <td id="6"></td>
                    <td id="7"></td>
                    <td id="8"></td>
                </tr>
            </table>
            <div style="width: 100px;text-align: center;margin: auto;margin-top: 65px;">
                <button id="rejouer" onclick="location.reload()">Rejouer</button></div>
        </div>
        <p id="result"> </p>
        <div id="ContQuit" style="display: none;">
            <button id="continue">Continue</button>
            <button id="Quit">Quit</button>
        </div>
        <div id="GenJoin">
            <div id="Gen">
                <label for="generatedSess">Send this session Number to the other player</label>
                <input name="generatedSess" id="generatedSess" Value="" readonly>
                <button id="creatSess">Creat Session</button>
            </div>
            <div id="join">
                <label for="enteredSess">Enter Session numbre and join a friend</label>
                <input type="text" id="enteredSess" name="eneteredSess">
                <button id="joinSess">Join Session</button>
            </div>
        </div>
        <script>
            var player1 = 0;
            var player2 = 0;

            function makeid(length) {
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for (var i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                return result;
            };

            function sleep(milliseconds) {
                const date = Date.now();
                let currentDate = null;
                do {
                    currentDate = Date.now();
                } while (currentDate - date < milliseconds);
            }

            $(document).ready(function() {
                $("#creatSess").click(function() {

                    window.code = makeid(6);
                    $.post(
                        'tictacTreatement.php', {
                            code: code
                        },
                        function() {
                            $("#generatedSess").val(code);
                            player1 = 1;
                            $("#join").hide();
                            $("#result").html("waiting for the other player to connect.");
                            var xmlhttp = new XMLHttpRequest();
                            var waitPlayer2 = setInterval(() => {
                                xmlhttp.onreadystatechange = function() {
                                    if (this.readyState == 4 && this.status == 200) {
                                        if (this.responseText == 1) {
                                            $("#result").html("Player 2 is connected.");
                                            $("#Gen").hide();
                                            document.getElementById("tab").style.visibility = "visible";
                                            clearInterval(waitPlayer2);
                                            Game();
                                        }
                                    }
                                };
                                xmlhttp.open("GET", "tictacTreatement.php?sess=" + code, true);
                                xmlhttp.send();
                            }, 1000);
                        },

                    );
                });
            });

            $(document).ready(function() {
                $("#joinSess").click(function() {
                    var code = $("#enteredSess").val();
                    $.post(
                        'tictacTreatement.php', {
                            codeEntered: code
                        },
                        function(data) {

                            if (data == "Joined game succesfully.") {
                                $("#result").html("Joined game succesfully.");
                                document.getElementById("tab").style.visibility = "visible";
                                player2 = 1;
                                $("#Gen").hide();
                                $("#join").hide();
                                Game();
                            }
                        },
                        'text'
                    )
                });
            })

            $(document).ready(function() {
                $("#continue").click(function() {
                    var playeractu = (player1 == 1) ? 1 : 2;
                    console.log("player : " + playeractu);
                    $("#result").html("waiting for the other player to continue.");
                    document.getElementById("tab").style.visibility = "hidden";
                    $.post(
                        'tictacTreatement.php', {
                            player: playeractu,

                        },
                        function() {
                            var req = new XMLHttpRequest()
                            var waitcontinue = setInterval(() => {
                                req.onreadystatechange = function() {
                                    if (this.status == 200 && this.readyState == 4) {
                                        if (this.responseText == 11) {
                                            document.getElementById("tab").style.visibility = "visible";
                                            $("#result").html("");
                                            player1 = (player1 == 1) ? 0 : 1;
                                            player2 = (player2 == 1) ? 0 : 1;
                                            clearInterval(waitcontinue);
                                            Game();

                                        } else
                                        if (this.responseText == 0) {
                                            player1 = 0;
                                            player2 = 0;
                                            $("#result").html("The Other player has left");
                                            $("#Gen").show();
                                            $("#join").show();
                                            clearInterval(waitcontinue);
                                        }
                                    }
                                }
                                req.open("GET", "tictacTreatement.php?cont=''", true);
                                req.send();
                            }, 500);
                        },
                    )
                });
            })
            $(document).ready(function() {
                $("#Quit").click(function() {
                    document.getElementById("tab").style.visibility = "hidden";
                    $.post(
                        'tictaCTreatement.php', {
                            coode: ""
                        },
                        function() {
                            $("#Gen").show();
                            $("#join").show();
                            $("#ContQuit").hide();
                            $("#result").html("");
                        }
                    )
                });
            })


            function Game() {
                for (var i = 0; i < 9; i++)
                    $("#" + i).html("");
                var numGames = 0;
                var tie = 1;

                if (player1) {
                    var turn = "X";
                    var opp = "O";
                    var go = 1;
                }
                if (player2) {
                    var turn = "O";
                    var opp = "X";
                    var go = 0;
                }
                if (go)
                    document.getElementById("winner").innerHTML = "<span class='" + turn + "' >Your turn : " + turn + "</span>";
                else
                    document.getElementById("winner").innerHTML = "<span class='" + turn + "' >Your opponenet's trun:" + opp + "</span>";

                var tds = document.getElementsByTagName("td");
                var tdsCount = tds.length;
                var filled = [];
                var vic = 0;

                function endgame() {
                    $(document).ready(function() {
                        $.post(
                            'tictacTreatement.php', {
                                gamecoode: code
                            },
                            function() {
                                $("#result").html("Would you like to continue ?");

                            },
                            'text'
                        )
                    })
                }

                function checkwin(player) {
                    //////// WINING CONDITIONS  ////////
                    /// Horizontale ///
                    if ((filled[0] == filled[1] && filled[0] == filled[2] && filled[0] != null) ||
                        (filled[3] == filled[4] && filled[3] == filled[5] && filled[3] != null) ||
                        (filled[6] == filled[7] && filled[6] == filled[8] && filled[6] != null)) {
                        document.getElementById("winner").innerHTML = "<span class='" + player + "' >" + player + " Wins!</span>";
                        vic = 1;
                        $("#ContQuit").show();
                        if (player = "X")
                            setTimeout(() => {
                                endgame();
                            }, 2000);
                        $("#result").html("Would you like to continue ?");
                        return (1);
                    }
                    /// Diagonale ///
                    if ((filled[0] == filled[4] && filled[0] == filled[8] && filled[0] != null) ||
                        (filled[2] == filled[4] && filled[2] == filled[6] && filled[2] != null)) {
                        document.getElementById("winner").innerHTML = "<span class='" + player + "' >" + player + " Wins!</span>";
                        vic = 1;
                        $("#ContQuit").show();
                        if (player = "X")
                            setTimeout(() => {
                                endgame();
                            }, 2000);
                        $("#result").html("Would you like to continue ?");
                        return (1);
                    }
                    /// Verticale ///
                    if ((filled[0] == filled[3] && filled[0] == filled[6] && filled[0] != null) ||
                        (filled[1] == filled[4] && filled[1] == filled[7] && filled[1] != null) ||
                        (filled[2] == filled[5] && filled[2] == filled[8] && filled[2] != null)) {
                        document.getElementById("winner").innerHTML = "<span class='" + player + "' >" + player + " Wins!</span>";
                        vic = 1;
                        $("#ContQuit").show();
                        if (player = "X")
                            setTimeout(() => {
                                endgame();
                            }, 2000);
                        $("#result").html("Would you like to continue ?");
                        return (1);
                    }
                    tie = 1;
                    for (var i = 0; i < 9; i++) {
                        if (filled[i] == null)
                            tie = 0;
                    }
                    if (tie) {
                        document.getElementById("winner").innerHTML = "<span >It's a TIE </span>";
                        $("#ContQuit").show();
                        if (player = "X")
                            setTimeout(() => {
                                endgame();
                            }, 2000);
                        $("#result").html("Would you like to continue ?");
                        return (2);
                    }
                }

                function WaitMove() {
                    var xhttp = new XMLHttpRequest()
                    var interval = setInterval(() => {
                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    if (this.responseText != null) {
                                        var tdId = this.responseText;
                                        if (filled[tdId] == null) {
                                            if (player1)
                                                filled[tdId] = "O";
                                            else
                                                filled[tdId] = "X";
                                            document.getElementById(tdId).innerHTML = "<span class='" + filled[tdId] + "' >" + filled[tdId] + "</span>";
                                            document.getElementById("winner").innerHTML = "<span class='" + turn + "' >Your trun:" + turn + "</span>";
                                            go = 1;
                                            checkwin(opp);
                                            clearInterval(interval);
                                        }
                                    }
                                }
                            };
                            xhttp.open("GET", "tictacTreatement.php?game=''");
                            xhttp.send();
                        },
                        500);
                }
                if (player2) {
                    WaitMove();
                }
                for (var i = 0; i <= tdsCount; i += 1) {
                    tds[i].onclick = function() {
                        var played = this.id;
                        if (filled[played] == null && !vic && go) {
                            document.getElementById(played).innerHTML = "<span class='" + turn + "' >" + turn + "</span>";
                            filled[played] = turn;
                            $(document).ready(function() {
                                $.post(
                                    'tictacTreatement.php', {
                                        round: '',
                                        played: played
                                    },
                                    function() {
                                        go = 0;
                                        document.getElementById("winner").innerHTML = "<span class='" + opp + "' >Your opponenet's trun:" + opp + "</span>";
                                        var check = checkwin(turn);
                                        if (check != 1 && check != 2)
                                            WaitMove();
                                    }
                                );
                            });
                        }
                    }
                }
            }
        </script>

    </div>
</body>

</html>