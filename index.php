<?php
session_start();

// Génération du token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

// Fonction de conversion du code DAM en date
function convertDateCode($dam) {
    // Le DAM 1 correspond au 9 novembre 1976
    $start = new DateTime('1976-11-09');
    $damInt = intval($dam);
    if ($damInt < 1) return 'Invalide';
    $date = clone $start;
    $date->modify('+' . ($damInt - 1) . ' days');
    return $date->format('d/m/Y');
}

// Tableau des codes usine
$factoryCodes = [
    'A2' => 'CENTRE HILLS ANGLETERRE',
    'CA' => 'CENTRE AULNAY',
    'CB' => 'CENTRE RENNES BT',
    'CC' => 'CENTRE DE CAEN',
    'CH' => 'CENTRE DE CHARLEVILLE',
    'CJ' => 'CENTRE RENNES-JANAIS',
    'CP' => 'CENTRE CHINE CKD AC',
    'CS' => 'CENTRE DE ST OUEN',
    'CV' => 'CENTRE MEUDON',
    'CX' => 'CENTRE ASNIERES',
    'FL' => 'CENTRE DE MANGUALDE',
    'FP' => 'CENTRE DE PINTO',
    'FT' => 'CENTRE DE TREMERY',
    'FV' => 'CENTRE DE VIGO',
    'FW' => 'CENTRE DE METZ',
    'G5' => 'CENTRE DE DOUVRIN',
    'G8' => 'CENTRE DE CREIL',
    'G9' => 'CENTRE DE GENEVILLIERS',
    'UB' => 'CENTRE DE NEWCO',
    'UC' => 'CENTRE DE OLCIT',
    'UD' => 'CENTRE DURISOT',
    'UF' => 'FORD',
    'UG' => 'GRUAU',
    'UH' => 'KALUGA',
    'UJ' => 'JAGUAR',
    'UK' => 'CENTRE CIMOS',
    'UM' => 'CENTRE MITSUBISHI',
    'UN' => 'CENTRE FIAT VILLANOVA',
    'UO' => 'CENTRE TOFAS',
    'US' => 'CENTRE SUZUKI',
    'UT' => 'CENTRE KOLIN',
    'UV' => 'VOLVO',
    'UW' => 'CENTRE DE WUHAN',
    'UX' => 'XIANG-FIANG',
    'UY' => 'CENTRE MAGNA STEYR',
    'UZ' => 'CENTRE KARSAN',
    'U1' => 'FIAT',
    'U3' => 'CENTRE DE PANHARD',
    'U5' => 'CENTRE PININ FARINA',
    'U6' => 'CENTRE DAIMLER',
    'U7' => 'CENTRE HEULIEZ',
    'U8' => 'CENTRE SEVEL SUD',
    'U9' => 'CENTRE SEVEL NORD',
    'YA' => 'CENTRE INDONESIE',
    'YB' => 'CENTRE CHILI',
    'YC' => 'CENTRE NIGERIA',
    'YD' => 'CENTRE URUGUAY',
    'YE' => 'CENTRE KENYA',
    'YF' => 'CENTRE ZIMBABWE',
    'YG' => 'CENTRE TURQUIE',
    'YH' => 'CENTRE POLOGNE',
    'YI' => 'CENTRE IRAN SAIPA',
    'YJ' => 'CENTRE EGYPTE',
    'YK' => 'CENTRE THAILANDE',
    'YL' => 'CENTRE MALAISIE',
    'YM' => 'CENTRE RUSSIE',
    'YN' => 'CENTRE IRAN KHODRO',
    'YP' => 'CENTRE MAROC',
    'YQ' => 'CENTRE MALAISIE GURUN',
    '7B' => 'BARUERI BRéSIL MAG PR',
    '7D' => 'FRANCAISE DE MECANIQUE',
    '71' => 'GEFCO',
    '72' => 'HERIMONCOURT',
    '73' => 'NANTERRE',
    '74' => 'CENTRE DE VALENCIENNES',
    '75' => 'SFME',
    '76' => 'SLOVAQUIE TRNAVA',
    '77' => 'ARGENTINE BUENOS AIRES',
    '78' => 'BRESIL PORTO REAL',
    '8E' => 'CENTRE DE SEPT FONS',
    '8F' => 'CENTRE SHERPA',
    '8M' => 'CENTRE DE MELUN',
    '81' => 'CENTRE DE POISSY',
    '82' => 'CENTRE DE MADRID',
    '83' => 'CENTRE DE RYTON',
    '84' => 'CENTRE DE DIJON',
    '85' => 'CENTRE DE VESOUL',
    '87' => 'CENTRE DE LILLE',
    '88' => 'CENTRE DE MULHOUSE',
    '89' => 'CENTRE DE SOCHAUX'
];

// Fonction pour obtenir le numéro DAM pour une date donnée
function getDamNumber($date) {
    $start = new DateTime('1976-11-09');
    $interval = $start->diff($date);
    return $interval->days + 1;
}

// Pour le tableau de conversion
$startYear = 1976;
$endYear = date('Y'); // Année actuelle
$months = [
    1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
    7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
];

$highlightYear = null;
$highlightMonth = null;

// Si un code DAM est soumis et valide, on surligne la cellule correspondante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['damCode']) && !empty($_POST['damCode'])) {
    $damCode = trim($_POST['damCode']);
    if (ctype_digit($damCode) && $damCode > 0 && strlen($damCode) <= 7) {
        try {
            $dateObj = new DateTime('1976-11-09');
            $dateObj->modify('+' . (intval($damCode) - 1) . ' days');
            $highlightYear = (int)$dateObj->format('Y');
            $highlightMonth = (int)$dateObj->format('n');
        } catch (Exception $e) {
            // Gérer l'erreur si la date est invalide
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertisseur de Code DAM Peugeot Citroën</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Convertisseur de Code DAM Peugeot Citroën</h1>
        
        <div class="converter-section">
            <form method="POST" action="" class="form-inline" id="damForm" autocomplete="off">
                <div class="input-group">
                    <label for="damCode">Code DAM :</label>
                    <input type="text" id="damCode" name="damCode" maxlength="7" placeholder="Entrez le code DAM (ex : 23, 784, 13203...)" value="<?php echo htmlspecialchars($_POST['damCode'] ?? ''); ?>" autocomplete="off">
                </div>
                <div class="input-group">
                    <label for="factoryCode">Code usine (optionnel) :</label>
                    <select id="factoryCode" name="factoryCode" autocomplete="off">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($factoryCodes as $code => $name): ?>
                            <option value="<?php echo htmlspecialchars($code); ?>" <?php if(isset($_POST['factoryCode']) && $_POST['factoryCode'] === $code) echo 'selected'; ?>><?php echo htmlspecialchars($code . ' - ' . $name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <button type="button" id="submitBtn" class="submit-btn">Convertir</button>
            </form>
            <div id="errorMessage" class="error-message" style="display: none;"></div>
            <div class="result-section">
                <h2>Résultat :</h2>
                <div id="result">
                    <p>Date de fabrication : <span id="manufacturingDate">-</span></p>
                    <p>Usine : <span id="factory">-</span> <span id="factoryName" style="color:#3498db;font-weight:bold;"></span></p>
                </div>
            </div>
        </div>

        <div class="calendar-section">
            <h2>Tableau des Codes DAM (Premier jour de chaque mois)</h2>
            <div class="table-container">
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th>Année/Mois</th>
                            <?php foreach ($months as $monthName): ?>
                                <th><?php echo $monthName; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($year = $startYear; $year <= $endYear; $year++): ?>
                            <tr>
                                <td><strong><?php echo $year; ?></strong></td>
                                <?php
                                $damStartDate = new DateTime('1976-11-09');
                                $currentDate = new DateTime();

                                for ($monthNum = 1; $monthNum <= 12; $monthNum++): // Loop through all 12 months for display consistency
                                    $date = DateTime::createFromFormat('Y-n-j', "$year-$monthNum-1");

                                    $displayContent = '<td>-</td>';
                                    if ($date >= $damStartDate && $date <= $currentDate) {
                                        $isHighlight = ($highlightYear === $year && $highlightMonth === $monthNum);
                                        $title = $date->format('d/m/Y');
                                        $damNumber = getDamNumber($date);
                                        // Ajout des attributs data-year et data-month
                                        $displayContent = '<td data-year="' . $year . '" data-month="' . $monthNum . '"' . ($isHighlight ? ' class="highlight-cell"' : '') . ' title="' . htmlspecialchars($title) . '">' . $damNumber . '</td>';
                                    } else {
                                        $displayContent = '<td>-</td>';
                                    }
                                    echo $displayContent;
                                endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>


        </div>
        <?php
        // Compteur de visites simple
        $compteur_file = __DIR__ . '/compteur.txt';
        if (!file_exists($compteur_file)) {
            file_put_contents($compteur_file, '0');
        }
        $visites = (int)file_get_contents($compteur_file);
        $visites++;
        file_put_contents($compteur_file, (string)$visites);
        ?>

        <div class="faq-section" itemscope itemtype="https://schema.org/FAQPage">
            <h2>Questions Fréquentes (FAQ)</h2>
            <div class="faq-item" itemscope itemtype="https://schema.org/Question">
                <h3 itemprop="name">Qu'est-ce qu'un code DAM ?</h3>
                <div class="faq-answer" itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
                    <p itemprop="text">Le code DAM (Date Application Modèle) est un numéro de série unique attribué à chaque véhicule Peugeot et Citroën, indiquant sa date de fabrication. Il est composé de 5 à 7 chiffres.</p>
                </div>
            </div>

            <div class="faq-item" itemscope itemtype="https://schema.org/Question">
                <h3 itemprop="name">Comment décoder mon code DAM ?</h3>
                <div class="faq-answer" itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
                    <p itemprop="text">Vous pouvez entrer votre code DAM dans le champ ci-dessus. Notre outil convertira le code en date de fabrication et, si vous entrez le code usine, il identifiera également l'usine de production.</p>
                </div>
            </div>
            
            <div class="faq-item" itemscope itemtype="https://schema.org/Question">
                <h3 itemprop="name">Où trouver le code DAM de mon véhicule ?</h3>
                <div class="faq-answer" itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
                    <p itemprop="text">Le code DAM est généralement gravé sur une étiquette située sur le montant de la porte conducteur, sous le capot, ou sur la carte grise (champ E ou Z.3 selon les pays).</p>
                </div>
            </div>
        </div>

        <div style="text-align:center; margin-top:2rem; color:#888; font-size:0.95rem;">
            Nombre de visites : <strong><?php echo $visites; ?></strong>
        </div>
        <div style="text-align:center; margin-top:0.5rem; color:#888; font-size:0.95rem;">
            <em>Ce site n'utilise aucun cookie, ni traceur publicitaire ou analytique. Aucune donnée personnelle n'est collectée ou stockée sur votre appareil. Le compteur de visites est purement technique et ne repose sur aucun cookie. (Conformité RGPD)</em>
        </div>

        <script src="script.js"></script>

    </div>

    <!-- Modal Structure -->
    <div id="monthModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 id="modalMonthYear"></h3>
            <div id="modalTableContainer" class="modal-table-container">
                <!-- Month table will be injected here by JavaScript -->
            </div>
        </div>
    </div>
</body>
</html> 