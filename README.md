# Convertisseur de Code DAM Peugeot & Citroën

Ce projet fournit :

1. Une interface web (`index.php`) permettant de convertir visuellement un **code DAM** en date de fabrication ;
2. Une **API JSON** (`api/DamConverter.php`) pour la même opération, destinée à un usage automatisé.

---

## Prérequis

* **PHP ≥ 8.0** (extensions standard activées)
* Un serveur Web (Apache, Nginx, ou le serveur PHP intégré)

> Aucune base de données ni dépendance externe n'est nécessaire.

---

## Installation

```bash
# Cloner le dépôt
git clone [<url-du-dépôt>](https://github.com/karimmarais/Code-DAM.git) code-dam
cd code-dam

```

Le site est alors accessible à `[votredomaine.tld]/index.php`.

---

## Utilisation de l'interface web

* Ouvrez `index.php` dans votre navigateur.
* Saisissez le **code DAM** (5 à 7 chiffres) ;
* (Facultatif) Sélectionnez un **code usine** pour identifier le site de production ;
* Cliquez sur **Convertir** pour obtenir la date correspondante.

---

## Utilisation de l'API

### Point d'entrée

`/api/DamConverter.php`

### Paramètres

| Nom | Type | Obligatoire | Description |
|-----|------|-------------|-------------|
| `dam` | entier | Oui | Code DAM numérique (> 0) |

Les requêtes **GET** et **POST** sont acceptées.

### Réponses

#### Succès (HTTP 200)

```json
{
  "success": true,
  "dam": 13203,
  "manufacturingDate": "05/06/2012"
}
```

#### Erreurs

| Code HTTP | Payload | Condition |
|-----------|---------|-----------|
| 400 | `{ "success": false, "error": "Paramètre \"dam\" manquant." }` | Paramètre absent |
| 422 | `{ "success": false, "error": "Code DAM invalide." }` | Valeur non numérique ou ≤ 0 |

### Exemples

```bash
# Requête GET
curl "https://[votredomaine.tld]/api/DamConverter.php?dam=13203"

# Requête POST
curl -X POST -d "dam=13203" https://[votredomaine.tld]/api/DamConverter.php
```

---

## Structure du projet

```
code-dam/
├── api/
│   └── DamConverter.php      # Point d'entrée de l'API
├── compteur.txt              # Compteur de visites (créé automatiquement)
├── index.php                 # Interface web principale
├── script.js                 # Logique JavaScript associée
├── styles.css                # Feuilles de style
└── README.md                 # (ce fichier)
```

---

## Licence

Ce projet est fourni **sans aucune garantie**. Pour toute demande de modification, merci de me contacter. 
K. MARAIS
