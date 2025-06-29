<?php
session_start();

// Génération du token CSRF si absent
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

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

$response = [
    'manufacturingDate' => '-',
    'factory' => '-',
    'factoryName' => '',
    'error' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $response['error'] = "Erreur de sécurité : le token CSRF est invalide.";
    } else {
        $code = trim($_POST['damCode']);
        if (empty($code)) {
            $response['error'] = "Veuillez entrer un code DAM";
        } elseif (!ctype_digit($code)) {
            $response['error'] = "Le code DAM doit être un nombre entier positif";
        } elseif (strlen($code) > 7) {
            $response['error'] = "Le code DAM ne doit pas dépasser 7 chiffres";
        } else {
            try {
                $response['manufacturingDate'] = convertDateCode($code);
                $factoryInput = trim($_POST['factoryCode'] ?? '');
                if ($factoryInput !== '') {
                    if (!array_key_exists($factoryInput, $factoryCodes)) {
                        $response['error'] = "Code usine non reconnu.";
                    } else {
                        $response['factory'] = $factoryInput;
                        $response['factoryName'] = $factoryCodes[$factoryInput] ?? '';
                    }
                }
            } catch (Exception $e) {
                $response['error'] = "Erreur lors de la conversion : " . $e->getMessage();
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response); 