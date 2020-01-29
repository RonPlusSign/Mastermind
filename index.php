<!-- 
  Andrea Delli, 26/01/2020
  Main file of MasterMind game
-->
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- stylesheets (normal & Bootstrap 4.4.1) -->
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

  <title>Mastermind</title>
</head>

<body>
  <h1>Master Mind Game</h1>
  <br>

  <?php /*//////////// PHP STARTS ////////////*/

  //global variables
  $ATTEMPTS_MAX = 10;
  $colors = array("red", "green", "blue", "yellow");

  //modal with the rules
  require "rules.html";

  //start or continue the session
  session_start();

  /* ---------- Functions ---------- */

  /**
   * Prints the right colors
   * Used for debug or when the user lose
   */
  function printFinalColors()
  {
    echo "
    <br>
      <table class='table' id='correct-colors'>
        <tr>
          <th>Right colors:</th>
          <td> <p class='circle " . $_SESSION["values"][0] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][1] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][2] . " '></p> </td>
          <td> <p class='circle " . $_SESSION["values"][3] . " '></p> </td>
        </tr>
      </table>
      ";
  }

  /**
   * Prints the attempts table (attempts table is a 2-dimensional array, stored in $_SESSION["attempts"])
   * Structure: N.attempt, colors, suggestions
   * 
   * Also prints how many colors are in the right or wrong position
   * $_SESSION["attempts"][4] contains how many colors are in the right position
   * $_SESSION["attempts"][5] contains how many colors are in the wrong position, even if the color is ok 
   */
  function printAttempts()
  {
    //print the table headers
    echo "<hr>
    <h5>Attempts made</h5>
    <table class='table' id='attempts'>
      <tr>
      <th scope='col col-md-1'>N°</th>
      <th scope='col col-md-1'>1</th>
      <th scope='col col-md-1'>2</th>
      <th scope='col col-md-1'>3</th>
      <th scope='col col-md-1'>4</th>
      <th scope='col col-md-4'>Suggestions</th>
      </tr>";


    if (isset($_SESSION["attempts"])) { //check if there are attempts saved
      echo "<tr>";

      foreach ($_SESSION["attempts"] as $attemptNumber => $row) {

        echo "<th>" . ($attemptNumber + 1) . "</th>"; //print the attempt number

        foreach ($row as $key => $value) {  //iterate each row
          if ($key == 4) {  //$_SESSION["attempts"][4] contains how many colors are in the right position

            echo "<td> ";

            for ($i = 0; $i < $value; $i++) {
              echo "<p class='circle black'></p>";
            }
          } elseif ($key == 5) {  //$_SESSION["attempts"][5] contains how many colors are in the wrong position, even if the color is ok

            for ($i = 0; $i < $value; $i++) {
              echo "<p class='circle white'></p>";
            }
            echo "</td>";
          } else echo "<td> <p class='circle  $value '></p> </td>"; //print the coloured circle
        }
        echo "</tr>";
      }
    }

    echo "</table>";
  }

  /** Function that checks how many values of $attempt are in $rightCombo, then saves in $_SESSION["attempts"][LAST_ATTEMPT][5]
   * The $attempt array should contain only values that aren't in the right position
   * This function should always be called only by rightPosition()
   * @param $attempt the attempt of the user
   * @param $rightCombo the correct combo to guess
   */
  function rightColor($attempt, $rightCombo)
  {
    $rightColorNumbers = 0;

    foreach ($attempt as $value) {
      if (array_search($value, $rightCombo) !== FALSE) {  //MUST use !==FALSE because array_search may return 0 (and its considered false if you just use != or similar)
        $rightColorNumbers++;

        unset($rightCombo[array_search($value, $rightCombo)]);  //remove the value from $rightCombo, to avoid possible problems when in $attempt there're same colors several times
      }
    }
    $_SESSION["attempts"][count($_SESSION["attempts"]) - 1][5] = $rightColorNumbers;  //save the final value
  }

  /** Function that checks how many colors of the attempt are in the correct position, then saves in $_SESSION["attempts"][LAST_ATTEMPT][4]
   * This function then calls the rightColor() method, to check for the number of colors in the wrong positon
   */
  function rightPosition()
  {
    $attempt = array($_POST["0"], $_POST["1"], $_POST["2"], $_POST["3"]);
    $rightCombo = $_SESSION["values"];
    $rightPositionNumber = 0;

    foreach ($rightCombo as $key => $val) {
      if ($val == $attempt[$key]) {
        $rightPositionNumber++;

        //if there's a match, remove the values from both arrays
        unset($attempt[$key]);
        unset($rightCombo[$key]);
      }
    }

    //store the result in $_SESSION["attempts"][LAST_ATTEMPT][4]
    $_SESSION["attempts"][count($_SESSION["attempts"]) - 1][4] = $rightPositionNumber;

    //check for the number of colors in the wrong position
    rightColor($attempt, $rightCombo);
  }

  /* ---------- Request logic ---------- */
  if (isset($_SESSION["values"])) { //check if there's a match saved (= some values to guess are stored in session)
    if (
      isset($_POST['0']) &&
      isset($_POST['1']) &&
      isset($_POST['2']) &&
      isset($_POST['3'])
    ) {

      //save the attempt
      if (isset($_SESSION["attempts"])) {

        //push another attempt in the $_SESSION["attempts"] array (2-dimensional array)
        array_push($_SESSION["attempts"], array($_POST["0"], $_POST["1"], $_POST["2"], $_POST["3"]));

        rightPosition();  //check for colors in right and wrong position

      } else {
        //create the attempt array ($_SESSION["attempts"] is a 2-dimensional array)
        $_SESSION["attempts"] = array(array($_POST["0"], $_POST["1"], $_POST["2"], $_POST["3"]));

        rightPosition();  //check for colors in right and wrong position
      }

      if (  //guessed
        $_POST["0"] == $_SESSION["values"][0] &&
        $_POST["1"] == $_SESSION["values"][1] &&
        $_POST["2"] == $_SESSION["values"][2] &&
        $_POST["3"] == $_SESSION["values"][3]
      ) {
        echo "<h1 class='label' id='win'>You win!</h1>
          <form action=\"index.php\" method=\"GET\">
              <button class=\"btn btn-outline-primary\" type=\"submit\">Play again</button>
          </form>";

        session_destroy();
      } else if (count($_SESSION["attempts"]) >= $ATTEMPTS_MAX) {  //end of player's attempts
        echo "<h1 class='label' id='lost'>You lost...</h1>
          <form action=\"index.php\" method=\"GET\">
              <button class=\"btn btn-outline-primary\" type=\"submit\">Play again</button>
          </form>";

        printFinalColors(); //warn the player of the right colors
        session_destroy();
      } else  require "form.html"; //neither win or lost
    } else require "form.html";  //if there are no values in the post request (useless?)
  } else {  //generate the values to guess when the game launches for the first time
    $_SESSION["values"] = array($colors[rand(0, 3)], $colors[rand(0, 3)], $colors[rand(0, 3)], $colors[rand(0, 3)]);
    require "form.html";
  }

  //printFinalColors(); // FOR DEBUG
  echo "<br><br>";

  //print the attempts list
  printAttempts();
  /*//////////// PHP ENDS ////////////*/ ?>

  <!-- Bootstrap, jQuery, Popper -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>