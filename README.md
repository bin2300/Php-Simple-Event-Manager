---

# 🎉 EventManager — Plateforme complète de gestion d'événements et de billets

> **EventManager** est une application web en PHP & MySQL permettant aux utilisateurs de s’inscrire à des événements, réserver des billets, générer des tickets PDF avec QR code, et aux administrateurs de gérer toute l’activité via une interface dédiée.

---

## 🗂 Sommaire

* [🎯 Objectif du projet](#-objectif-du-projet)
* [🧰 Fonctionnalités](#-fonctionnalités)

  * [Utilisateur](#utilisateur)
  * [Administrateur](#administrateur)
* [🏛️ Architecture du projet](#-architecture-du-projet)
* [🛠️ Installation complète](#️-installation-complète)
* [🗄️ Base de données](#️-base-de-données)
* [🧾 Génération de billets PDF](#-génération-de-billets-pdf)
* [📊 Statistiques et gestion admin](#-statistiques-et-gestion-admin)
* [🧪 Comptes de test](#-comptes-de-test)
* [🚀 Fonctionnalités futures](#-fonctionnalités-futures)
* [📂 Dépendances](#-dépendances)
* [🤝 Contributions](#-contributions)
* [📄 Licence](#-licence)

---

## 🎯 Objectif du projet

L’objectif de ce projet est de créer une **plateforme complète de gestion d'événements** avec :

* Une interface utilisateur simple pour la consultation et la réservation
* Une interface d'administration riche pour la gestion
* Un système de tickets automatisé et sécurisé

---

## 🧰 Fonctionnalités

### 👥 Utilisateur

* Inscription / Connexion / Déconnexion
* Profil modifiable (nom, email, mot de passe)
* Liste des événements disponibles
* Réservation de billets
* Paiement simulé (prochainement paiement en ligne)
* Téléchargement de tickets PDF avec QR Code
* Génération de tickets par événement ou globaux
* Affichage des réservations passées
* Scan du QR code possible pour validation (à venir)

### 🛡️ Administrateur

* Tableau de bord complet des réservations
* Filtre par événement ou utilisateur
* Affichage du prix total par réservation
* Statistiques globales (CA, billets vendus, etc.)
* Actions : Supprimer une réservation, voir les détails
* Vérification des billets par QR code
* Création / édition / suppression d'événements (à venir)

---

## 🏛️ Architecture du projet

```bash
EventManager/
│
├── public/                # Frontend public (login, events, register...)
│   ├── profile.php
│   └── download_ticket.php
│
├── admin/                 # Tableau de bord admin
│   ├── bookings.php
│   └── events.php
│
├── includes/
│   ├── db/                # Connexion à la base de données
│   │   └── Database.php
│   ├── models/            # Modèles PHP représentant les entités
│   ├── controllers/       # Traitements des formulaires (ajout, update, delete)
│   ├── fpdf/              # Librairie FPDF
│   └── utils/             # Fonctions QR code, helpers
│
├── assets/                # CSS, JS, images
│   ├── styles.css
│   └── logo.png
│
└── README.md
```

---

## 🛠️ Installation complète

### 1. 📥 Clonage du dépôt

```bash
git clone https://github.com/ton-compte/EventManager.git
cd EventManager
```

### 2. 🛢️ Configuration de la base de données

* Crée une base de données `event_manager`
* Importer le fichier `database/schema.sql` dans phpMyAdmin ou via MySQL :

```bash
mysql -u root -p event_manager < database/schema.sql
```

* Modifier `includes/db/Database.php` :

```php
private $host = "localhost";
private $username = "root";
private $password = "";
private $dbname = "event_manager";
```

### 3. ⚙️ Configuration locale

* Placer le projet dans `htdocs` ou `/var/www/html/`
* Lancer XAMPP/LAMP et accéder via :

```
http://localhost/EventManager/public/
```

---

## 🗄️ Base de données

### Tables principales :

* `users` : stocke les utilisateurs (nom, email, mot de passe, rôle)
* `events` : titre, description, lieu, date, image
* `tickets` : chaque ticket est lié à un `event_id` (type, prix)
* `bookings` : en-tête d’une réservation (user\_id, date)
* `booking_items` : tickets réservés (ticket\_id, quantity, price)

---

## 🧾 Génération de billets PDF

* Utilise **FPDF** pour générer des billets
* QR Code généré avec `phpqrcode`
* Billets disponibles en deux formats :

  * Billet global avec tous les tickets
  * Billets individuels par événement
* Chaque billet contient :

  * Nom + Email de l’utilisateur
  * Détails du ticket
  * QR Code valide pour contrôle à l’entrée
* Téléchargement déclenché automatiquement après réservation

---

## 📊 Statistiques et gestion admin

L’interface `admin/bookings.php` fournit :

* Le nombre total de réservations
* Le chiffre d'affaires total
* La liste complète des réservations (triables, filtrables)
* Les options pour supprimer une réservation
* Le statut de chaque réservation (`payée`, `en attente`, `annulée`)

Filtres intégrés :

* Par utilisateur (nom, email)
* Par événement (titre)

---

## 🧪 Comptes de test

### Compte administrateur

```bash
Email : admin@example.com
Mot de passe : admin123
```

### Compte utilisateur

```bash
Email : user@example.com
Mot de passe : user123
```

> ⚠️ Change les mots de passe après installation.

---

## 🚀 Fonctionnalités futures

* Gestion complète des événements (CRUD)
* Paiement en ligne (Stripe ou PayPal)
* Validation des billets via QR Scanner mobile
* Dashboard analytique avec graphiques (Chart.js)
* Notifications email de confirmation
* Rôle organisateur (multi-admins)
* Export CSV des réservations

---

## 📂 Dépendances

* PHP 7.4+
* MySQL 5.7+
* [FPDF](http://fpdf.org/) – PDF Generation
* [PHP QR Code](https://sourceforge.net/projects/phpqrcode/)
* Bootstrap 4.5
* jQuery Slim

---

## 🤝 Contributions

Les contributions sont les bienvenues :

1. Fork ce dépôt
2. Crée ta branche `feature/ma-nouvelle-fonction`
3. Commit tes modifications
4. Push la branche
5. Crée une Pull Request

---

## 📄 Licence

**MIT License**

> Ce projet est open-source. Tu peux l'utiliser, le modifier et le redistribuer librement.

---

## 📬 Contact

Pour toute question ou remarque, tu peux me contacter à :

```
📧 ton.email@example.com
```

---

💡 **Astuce** : Pour une gestion multi-utilisateur plus propre, pense à ajouter un middleware de vérification de rôle et un système de permissions.

---

