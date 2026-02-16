# Parfum Shop — Projet PHP E-commerce (B2)

Site e-commerce dynamique en PHP + MySQL (XAMPP).
Thème : vente de parfums.

## Fonctionnalités
### Front-office
- Accueil + catalogue dynamique
- Fiche produit (si disponible)
- Inscription / Connexion (password_hash + password_verify)
- Panier (multi-produits, quantités +/-, total)
- Commande (création orders + invoice + décrément stock)
- Mes commandes (historique)

### Back-office (admin)
- Accès admin (rôle admin)
- CRUD produits (ajouter / modifier / supprimer)
- Liste utilisateurs (suppression non-admin)

## Installation (local)
### 1) Pré-requis
- XAMPP (Apache + MySQL)
- Navigateur
- VS Code (optionnel)

### 2) Mettre le projet dans XAMPP
Copier le dossier du projet ici :
`C:\xampp\htdocs\ProjetPHP`

### 3) Démarrer XAMPP
Ouvrir XAMPP Control Panel :
- Start **Apache**
- Start **MySQL**

### 4) Importer la base de données
1. Aller sur `http://localhost/phpmyadmin`
2. Créer une base : `parfum_shop`
3. Onglet **Importer**
4. Importer le fichier : `database.sql`

### 5) Lancer le site
- Accueil : `http://localhost/ProjetPHP/`
- Catalogue : `http://localhost/ProjetPHP/pages/catalog.php`
- Panier : `http://localhost/ProjetPHP/pages/cart.php`

## Comptes
### Admin
- Email : `admin@parfumshop.local`
- Mot de passe : `Admin123!` (si tu l’as reset)

### Utilisateur
Créer via : `http://localhost/ProjetPHP/pages/register.php`

## Structure (simplifiée)
- `pages/` : pages front (catalog, cart, checkout, login, register…)
- `admin/` : back-office (CRUD produits, users)
- `includes/` : header/footer + helpers
- `config/db.php` : connexion PDO MySQL

## Sécurité
- Requêtes préparées (PDO)
- Hash des mots de passe (password_hash)
- Validation basique des formulaires
