# BoutiqueEnLigne
est un projet de E-commerce réalisé avec symfony 5.4 et Twig en integrant Stripe pour le paiement en ligne avec la carte bancaire. 
Démarrage de serveur symfony avec la commande : <b>symfony server:start</b>

- page d'accueil : Affiche des produits par catégorie.
- formulaire d'inscription : permet à l'utilisateur de s'inscrire sur le site en renseignant les informations demandées.
- formulaire de connexion : pour faire un achat  sur le site il faut s'authentifier avec une adresse email et un mot de passe.  
- page produit : affiche les caractéristiques de chaque produit.
- page boutique : pour filtrer les produits du magasin  par catégories, par prix ou par mot-clé.
- page panier : l’utilisateur peut ajouter un ou plusieurs produits à son panier.

 la partie back-office du site  est créée avec le bundle EasyAdmin de symfony,   
- qui permet d’avoir une interface d’administration sur les entités ( PRODUCT, ORDER, CART, CATEGORIES, TRANSPORTER, CONTACT).
