
    <?php
    include('./simple_html_dom.php');
    include('./iRadovi.php');
    class DiplomskiRadovi implements iRadovi
    {
        public $naziv_rada;
        public $tekst_rada;
        public $link_rada;
        public $oib_tvrtke;
        private $db;

        // Konstruktor klase koji se poziva prilikom stvaranja objekta, što znaći da će se konekcija na bazu podataka stvoriti prilikom stvaranja objekta
        public function __construct()
        {
            $host = 'localhost';
            $dbname = 'radovi';
            $username = 'root';
            $password = '';

            // Spajanje na bazu podataka
            try {
                $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        // Metoda create koja dohvaća podatke sa stranice i popunjava objekt
        public function create($url)
        {
            // Dohvaćanje HTML sadržaja stranice
            $html_content = file_get_contents($url);

            // Kreiranje DOM objekta
            $html = new simple_html_dom();
            $html->load($html_content);

            // Pronalazak svih tagova article unutar article elemenata
            foreach ($html->find('article') as $article) {
                // Pronalazak slika unutar article elemenata
                foreach ($article->find('img') as $img) {
                    $filename = pathinfo($img->src, PATHINFO_FILENAME);
                    $this->oib_tvrtke = $filename;
                }

                // Pronalazak linkova i naziva rada unutar article elemenata
                foreach ($article->find('a.fusion-rollover-link') as $a) {
                    $this->link_rada = $a->href;
                    $this->naziv_rada = $a->plaintext;
                }

                // Pronalazak teksta rada unutar article elemenata
                foreach ($article->find('p') as $p) {
                    $this->tekst_rada = $p->plaintext;
                }

                // Spremanje podataka u bazu
                $this->save();
            }
        }

        // Metoda za ispisivanje podataka
        public function read()
        {
            try {
                // Priprema SQL upita
                $stmt = $this->db->prepare("SELECT naziv_rada, tekst_rada, link_rada, oib_tvrtke FROM diplomski_radovi");

                // Izvrši SQL upit
                $stmt->execute();

                // Dohvati rezultate
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Ispis rezultata
                foreach ($results as $row) {
                    echo "Naziv rada: " . $row['naziv_rada'] . "<br>";
                    echo "Tekst rada: " . $row['tekst_rada'] . "<br>";
                    echo "Link rada: " . $row['link_rada'] . "<br>";
                    echo "OIB tvrtke: " . $row['oib_tvrtke'] . "<br>";
                    echo "<hr>";
                }
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }

        // Metoda za spremanje podataka
        public function save()
        {
            try {
                $stmt = $this->db->prepare("INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke) VALUES (:naziv_rada, :tekst_rada, :link_rada, :oib_tvrtke)");
                $stmt->bindParam(':naziv_rada', $this->naziv_rada);
                $stmt->bindParam(':tekst_rada', $this->tekst_rada);
                $stmt->bindParam(':link_rada', $this->link_rada);
                $stmt->bindParam(':oib_tvrtke', $this->oib_tvrtke);
                $stmt->execute();
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
        }
    }
    ?>
