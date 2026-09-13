# Migration du site CERER vers Hostinger

Ce dossier contient tout ce qu'il faut pour déployer le site sur un domaine
Hostinger. Le dépôt Git ne contient volontairement QUE le travail propre à
ce site (thème enfant `cerer-child`, médiathèque, traduction anglaise) —
pas le cœur WordPress ni les plugins tiers, réinstallables tels quels
depuis les sources officielles indiquées ci-dessous.

## 1. Ce qu'il faut recréer sur Hostinger (pas fourni par le dépôt)

| Élément | Source | Version exacte utilisée en local |
|---|---|---|
| WordPress (cœur) | wordpress.org/download | **7.1** |
| PHP | fourni par Hostinger (à sélectionner dans hPanel) | **8.2** (minimum requis par le thème/les plugins) |
| MySQL / MariaDB | fourni par Hostinger | 8.4 (ou MariaDB équivalent) |
| Thème parent **Blocksy** | wordpress.org/themes/blocksy | **2.1.57** |
| Plugin **Polylang** (gratuit) | wordpress.org/plugins/polylang | **3.8.9** |
| Plugin **Advanced Custom Fields** (gratuit — PAS ACF Pro) | wordpress.org/plugins/advanced-custom-fields | **6.8.10** |

⚠️ Le site est bâti entièrement sur la version **gratuite** d'ACF (pas de
Repeater, Gallery, Options Page ni Flexible Content) — ne pas installer
ACF Pro à sa place, les champs numérotés du thème s'appuient sur les noms
de champs exacts de la version gratuite.

## 2. Contenu de ce dossier

- `MIGRATION-HOSTINGER.md` — ce fichier.
- `cerer-db-export-YYYYMMDD.sql` — export complet de la base de données
  locale (contenu : pages, articles, chercheurs, webinaires, projets,
  publications, réglages ACF, menus, réglages Polylang…). **Contient
  l'URL locale `http://cerer.local`** partout dans le contenu (articles,
  champs ACF, widgets) : l'étape 5 ci-dessous est obligatoire.
- `wp-config-sample.php` (à la racine du dépôt) — modèle pour recréer
  `wp-config.php` sur Hostinger avec les vraies informations de connexion
  (jamais commité, il contient des secrets).

## 3. Étapes de migration

### 3.1 Créer l'hébergement et le domaine sur Hostinger
1. Dans hPanel, créer (ou pointer) le domaine souhaité vers l'hébergement.
2. Créer une base de données MySQL dédiée (nom, utilisateur, mot de passe)
   depuis hPanel → Bases de données.
3. Noter : nom de la base, utilisateur, mot de passe, hôte (généralement
   `localhost` chez Hostinger).

### 3.2 Installer WordPress
- Soit via l'auto-installeur Hostinger (le plus simple), soit en
  téléchargeant WordPress 7.1 depuis wordpress.org et en l'uploadant par
  FTP/gestionnaire de fichiers hPanel.
- Ne pas terminer l'assistant d'installation web : on va écraser la base
  fraîchement créée par l'export du site local à l'étape 3.4.

### 3.3 Déployer le code du dépôt Git
Sur Hostinger (SSH si le plan le permet, sinon upload manuel du zip) :
```
git clone <url-du-depot> temp-cerer
cp -r temp-cerer/wp-content/themes/cerer-child   /chemin/vers/wp-content/themes/
cp -r temp-cerer/wp-content/uploads/*            /chemin/vers/wp-content/uploads/
cp -r temp-cerer/wp-content/languages/themes/*   /chemin/vers/wp-content/languages/themes/
cp temp-cerer/.htaccess                          /chemin/vers/.htaccess
```
Puis réinstaller Blocksy, Polylang et ACF (gratuit) depuis leurs pages
wordpress.org respectives (via l'admin WordPress une fois la base
restaurée, c'est plus simple qu'en manuel).

### 3.4 Importer la base de données
1. Dans hPanel → phpMyAdmin, ouvrir la base créée à l'étape 3.1.
2. Importer `cerer-db-export-YYYYMMDD.sql`.
3. Si le préfixe de table diffère de `wp_` sur Hostinger, adapter
   `wp-config.php` en conséquence (`$table_prefix = 'wp_';` doit
   correspondre à l'export).

### 3.5 Remplacer les URLs (`cerer.local` → domaine réel)
**Étape critique** : le contenu (articles, champs ACF, menus, options
Polylang) référence encore `http://cerer.local` en dur à de nombreux
endroits. Un simple import ne suffit pas.

Utiliser un plugin de recherche-remplacement compatible sérialisation PHP
(WordPress stocke des tableaux sérialisés — un remplacement texte brut
casserait ces données) :
- **Better Search Replace** (gratuit, wordpress.org/plugins/better-search-replace)
  ou WP-CLI (`wp search-replace 'http://cerer.local' 'https://votredomaine.com'`)
  si le plan Hostinger donne accès SSH + WP-CLI.
- Remplacer dans TOUTES les tables, y compris `wp_options` (menus, réglages
  ACF de type page_type/front_page, réglages Polylang) et les tables de
  métadonnées (`wp_postmeta`, `wp_termmeta`).
- Penser aussi à remplacer `http://` par `https://` si Hostinger active le
  SSL gratuit (recommandé) — soit en une seule passe avec la bonne URL
  cible, soit en deux passes successives.

### 3.6 wp-config.php
Copier `wp-config-sample.php`, renseigner :
- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST` (valeurs de l'étape 3.1).
- Les clés de sécurité (`AUTH_KEY` etc.) : générer un nouveau jeu sur
  https://api.wordpress.org/secret-key/1.1/salt/ — ne jamais réutiliser
  celles de l'environnement local.
- `WP_HOME` / `WP_SITEURL` si le thème ou un plugin les définit en dur
  (à vérifier, absent du wp-config local actuel).

### 3.7 Vérifications post-migration
- [ ] Réglages → Général : Adresse WordPress / Adresse du site sur le bon domaine.
- [ ] Réglages → Permaliens : cliquer "Enregistrer" une fois (régénère les
      règles de réécriture — `/%postname%/` actuellement).
- [ ] Polylang → Langues : les 2 langues (fr par défaut, en) et leurs
      drapeaux/slugs sont corrects ; Réglages → Lecture pointe vers les
      bonnes pages d'accueil et pages d'articles pour chaque langue.
- [ ] ACF → vérifier que les groupes de champs sont bien listés (ils sont
      en JSON dans le thème, auto-synchronisés — pas de réimport manuel
      nécessaire).
- [ ] Parcourir le menu complet dans les deux langues (voir
      `presentation-site-cerer.pdf` pour la liste) : aucun lien mort.
- [ ] Vérifier que les vidéos YouTube intégrées (miniatures mises en cache
      en médiathèque) se sont bien importées avec les fichiers uploads.
- [ ] Activer le certificat SSL gratuit Hostinger et forcer HTTPS.
- [ ] Supprimer le dossier `temp-cerer` et tout fichier d'installation
      WordPress résiduel (`wp-config-sample.php` à la racine, etc.).

## 4. Après la migration

- Le dépôt Git local peut être ajouté comme remote pour continuer à
  suivre les évolutions du thème (`git remote add production <url>`),
  ou servir uniquement de sauvegarde/historique — au choix.
- Toute évolution future du thème peut être poussée sur Hostinger via
  Git (si SSH disponible) ou en re-copiant `wp-content/themes/cerer-child`.
