<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Uključivanje datoteke s definicijom sučelja
    include('./DiplomskiRadovi.php');

    // Stvaranje objekta klase DiplomskiRadovi i pokretanje njenih metoda
    $rad = new DiplomskiRadovi();
    $rad->create('http://stup.ferit.hr/index.php/zavrsni-radovi/page/5');
    $rad->read();

    ?>
</body>

</html>