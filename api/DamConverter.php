<?php
// api/DamConverter.php
// Classe et point d'entrée API permettant de convertir un code DAM Peugeot/Citroën en date de fabrication.

header('Content-Type: application/json; charset=utf-8');

class DamConverter
{
    // Date de référence : le DAM 1 correspond au 9 novembre 1976
    private const START_DATE = '1976-11-09';

    /**
     * Convertit un code DAM numérique en date de fabrication.
     *
     * @param int|string $dam Code DAM (entier positif)
     * @return string|null    Date au format "d/m/Y" ou null si le code est invalide
     */
    public static function convert(int|string $dam): ?string
    {
        // Validation : uniquement des chiffres et > 0
        if (!ctype_digit((string)$dam) || (int)$dam < 1) {
            return null;
        }

        $damInt = (int)$dam;
        $start  = new DateTime(self::START_DATE);
        $start->modify('+' . ($damInt - 1) . ' days');

        return $start->format('d/m/Y');
    }

    /**
     * Gère la requête HTTP (GET ou POST) et renvoie la réponse JSON.
     * Paramètre attendu : "dam" (numérique).
     *
     * Exemple : /api/DamConverter.php?dam=13203
     */
    public static function handleRequest(): void
    {
        $dam = $_GET['dam'] ?? $_POST['dam'] ?? null;

        if ($dam === null) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error'   => 'Paramètre "dam" manquant.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $date = self::convert($dam);

        if ($date === null) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error'   => 'Code DAM invalide.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode([
            'success'          => true,
            'dam'              => (int)$dam,
            'manufacturingDate'=> $date
        ], JSON_UNESCAPED_UNICODE);
    }
}

// Exécute automatiquement la méthode si le fichier est appelé directement
DamConverter::handleRequest(); 