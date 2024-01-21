<?php
session_start();   // session is started
// Esto es necesario para almacenar y recuperar la información del juego a lo largo de las solicitudes del usuario.

//========================================================
//SELECT RANDOM WORD OK
// seleccionamos el json
// lo pasamos aun array asociativo
//elegimos un numero random
// retorna la palabra index en mayusuclas


function selectWord()
{
    $wordsJson = file_get_contents("words.json");
    $wordsArray =  json_decode($wordsJson,true);
    $words= $wordsArray["ingredientes_pizza"];
    $index = array_rand($words);
    return strtoupper($words[$index]);
}


echo ( selectWord());



//========================================================
// MUESTRA LETRAS ACERTADAS Y ESPACIOS EN BLANCO y hace un array llamdo letterOk
//  foreach (str_split($word) as $letter) cada palabra es un array de elementos

function showWord ($word, $letterOk)
{
    $showWord = "";
    foreach (str_split($word) as $letter) {  // convierte la palabra en un array de letras
        $showWord .= (in_array($letter, $letterOk)) ? $letter : '_';  // compara la leyts letter del cada array con letterok que se introduce en html
                                                                     // si es true la añade al array $showWord y si no es añade _ 
        $showWord .= ' ';
    }
    return trim($showWord);
}

//========================================================
//SUMA INTENTOS EN UN ARRAY; ACUMULA 1.2.3.4 hasta 6 no se porque
function showAttempts($attempts)
    {
        return implode(', ', $attempts);
    }
//========================================================
// Inicia variables de session si no esta f¡definida la varaible word ,se cargan el resto de variables 
    if (!isset($_SESSION['word'])) {                       //!isset es si no está definida
        $_SESSION['word'] = selectWord();
        $_SESSION['letterOk'] = array();
        $_SESSION['attempts'] = 6;
    }

//========================================================
//

    if (isset($_POST['letter'])) {
        $letter = strtoupper($_POST['letter']);

        if (preg_match('/^[A-Z]$/', $letter)) {
            if (in_array($letter, $_SESSION['letterOk']) || strpos($_SESSION['word'], $letter) === false) {
                // Letra repetida o incorrecta
                $_SESSION['attempts']--;
            } else {
                // Letra correcta
                $_SESSION['letterOk'][] = $letter;
            }
        }
    }

    if ($_SESSION['attempts'] == 0) {
        $mensaje = "¡Perdiste! La palabra era: " . $_SESSION['word'];
        session_destroy();
    } elseif (strpos(showWord ($_SESSION['word'], $_SESSION['letterOk']), '_') === false) {
        $mensaje = "¡Ganaste! La palabra es: " . $_SESSION['word'];
        session_destroy();
    } else {
        $mensaje = "Adivina la palabra: " . showWord ($_SESSION['word'], $_SESSION['letterOk']);
    }

  ?>

 <!DOCTYPE html>
 <html lang="es">
<head>
     <meta charset="UTF-8">
     <title>Game of Pizza</title>
 </head>
 <body>
     <h1>Game of Pizza</h1>
     <p><?php echo $mensaje; ?></p>
     <p>¿Que ingrediente es?: <?php echo showWord ($_SESSION['word'], $_SESSION['letterOk']); ?></p>   <!-- aqui muestr el array  de  showWord -->
     <p>Intentos restantes: <?php echo $_SESSION['attempts']; ?></p>
     <p>Letras incorrectas: <?php echo showAttempts(array_diff($_SESSION['letterOk'], str_split($_SESSION['word']))); ?></p>
     <p>Letras incorrectas: <?php print_r(array_diff($_SESSION['letterOk'], str_split($_SESSION['word']))); ?></p>

     <?php if ($_SESSION['attempts'] > 0 && strpos($mensaje, 'Ganaste') === false && strpos($mensaje, 'Perdiste') === false) { ?>
    <form method="post" action="">
        <label for="letter">Introduce una letra:</label>
        <input type="text" name="letter" maxlength="1" pattern="[A-Za-z]" required>
        <button type="submit">Adivinar</button>
    </form>
<?php } ?>
 </body>
 </html>
 