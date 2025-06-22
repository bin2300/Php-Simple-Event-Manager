event-booking-system/
│
├── 📁 public/                  # Dossier accessible publiquement (serveur web pointe ici)
│   ├── 📁 css/                 # Fichiers CSS
│   │   └── styles.css
│   ├── 📁 js/                  # Scripts JavaScript (front)
│   │   └── main.js
│   ├── 📁 images/              # Images utilisées (logos, événements)
│   ├── 📁 uploads/             # Images chargées par l'admin (événements, QR codes)
│   ├── 📄 index.php            # Page d'accueil (liste d'événements)
│   ├── 📄 login.php            # Page de connexion
│   ├── 📄 register.php         # Page d’inscription
│   ├── 📄 event.php            # Détails d’un événement
│   ├── 📄 cart.php             # Panier d’achat
│   ├── 📄 checkout.php         # Paiement
│   ├── 📄 dashboard.php        # Tableau de bord utilisateur
│   └── 📄 admin.php            # Page d’accueil admin (accès restreint)
│
├── 📁 includes/               # Fichiers PHP réutilisables (fonctions, base, sécurité)
│   ├── db.php                 # Connexion à la base de données
│   ├── auth.php               # Fonctions de login/session
│   ├── utils.php              # Fonctions utilitaires (format date, QR code, etc.)
│   └── header.php             # Header HTML commun
│   └── footer.php             # Footer HTML commun
│
├── 📁 admin/                  # Interface et scripts admin
│   ├── 📄 add_event.php
│   ├── 📄 edit_event.php
│   ├── 📄 delete_event.php
│   └── 📄 reports.php
│
├── 📁 sql/                    # Scripts SQL de création de base de données
│   └── schema.sql             # Tables: users, events, bookings, etc.
│
├── 📁 docs/                   # Rapport de projet, maquettes, documentation
│   ├── 📄 README.md
│   ├── 📄 rapport_final.pdf
│   └── 📄 wireframes.png
│
└── 📄 .htaccess               # Pour redirections ou sécurité Apache (optionnel)
