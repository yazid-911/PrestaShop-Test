# Module Itrmanagecontent 

---

## 1. Mise en place initiale   
  - Installation de Prestashop et creation du module "itrmanagecotent"
  - Fichiers de base (`itrmanagecontent.php`, `install.php`, `uninstall.php`, dossier `views/…`)  
  - Enregistrement des hooks essentiels (`header`, `displayBackOfficeHeader`, `displayHome`, `displayTop`, `displayFooterProduct`, ...)
  - Configuration de Prestashop avec XAMPP
  - lancement local sur VScode et localhost
  - creation de la base de donnees sur phpMyAdmin

---

## 2. Bloc HTML pour les visiteurs et les utilisateurs connectes
   - affichage back office en utilisant "getConfigForm()"
   ![Alt text](<Screenshot 2025-08-06 230738.png>)
   - affichage front office en utilisant "hookDisplayHome()"
   ![Alt text](<Screenshot 2025-08-06 231427.png>)



---

## 3. Statistiques sur le back office  
- affichage des statistiques en utilisant "getContent()" en appelant "renderStats()" pour afficher :  
     - Clients actifs  
     - Commandes validées  
     - Total des ventes  
    
    ![Alt text](<Screenshot 2025-08-06 230815.png>)


---

## 4. Statistiques sur le front office  
- affichage des statistiques en utilisant "hookDisplayHome()" pour afficher :
    - Le nombre de produits total actifs sur le site
    - Le prix du panier moyen
    - Lien vers le produit le plus commandé

    ![Alt text](image.png)


## Où je suis arrivé :

## 4. Signalement d’erreur produit (feature AJAX)  
  - Hook "displayFooterProduct()"
  - Injection du template "error_report_button.tpl" juste sous le bouton « Ajouter au panier ».
  - Ajout d’un bouton Signaler une erreur
  - Affichage d’une modal (jQuery) contenant :
  - Une textarea pour la description
  - Un bouton Envoyer et un bouton Fermer
  - Comportement JavaScript
  - Ouverture/fermeture de la modal sans rechargement de la page
  - Fermeture automatique de la modal
  - Traitement serveur (submitError in itrmanagecontent.php)
  - Insertion du signalement dans une table error_reports
  - Envoi d’un email à l’administrateur 

C’est la solution que j’avais envisagée pour cette fonctionnalité, j’ai commencé son développement, mais j’ai rencontré des difficultés, en particulier pour connecter correctement le bouton à la fenêtre modale d’envoi de message, ce qui m’a empêché de terminer la mise en œuvre.

---

# Ce qu’il reste à finaliser

**Fonctionnalités “Avatar”** 
   - Sélection d’avatars dans la fiche client  
   - Affichage sur la page d’accueil :  
     ```
     Bonjour {Prénom} {Avatar}
     ```

---

## Perspectives d’évolution  
- Restylage CSS 
- Recherche / filtrage en temps réel dans l’historique  
- Catégorisation des signalements
  Ajouter un champ « type d’erreur » (description, prix, image) pour mieux filtrer et traiter.

