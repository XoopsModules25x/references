<?php exit(); ?>
*******
v 1.8
*******
- Ajout d'une zone permettant d'afficher du texte complémentaire sur la page de détail d'une référence
- Dans l'administration, ajout d'un lien permettant d'aller directement voir les blocs du module
- Dans les blocs (références récentes et références au hasard), possibilité de choisir les catégories à afficher
- Ajout de 2 préférences pour choisir la zone de tri par défaut dans la liste des références dans l'admin
- Dans la liste des références, dans l'admin, ajout de la colonne "poids"
- Modification du module pour qu'il puisse être utilisée par d'autres modules 

Notes :
- Le module doit être mis à jour et il faut régler les préférences du module


*******
v 1.72
*******
- Mise à jour de la librairie utilisée pour redimensionner les images


*******
v 1.71
*******
- Correction d'un bug dans la visualisation des catégories (plus aucune catégorie visible)
- Correction d'un bug lors de la maintenance des tables


*******
v 1.7
*******
- Le module nécessite Xoops >= 2.3.3
- Ajout d'une page qui liste les catégories (category.php => references_category.html)
- Ajout d'une page qui affiche le contenu d'une référence (reference.php => references_reference.html)
- Modifications du template references_index.html (ajout d'une liste déroulante des catégories)
- Modification de la recherche pour que le lien pointe vers la page d'une référence
- Modification du plugin sitemap
- Ajout de tests pour vérifier la présence du module TAG
- Ajout d'une notification sur la création d'une catégorie
- Dans l'administration des catégories, le titre de chaque catégorie est cliquable
- Dans les pages côté utilisateur, ajout de liens vers l'administration
- Côté utilisateur, les catégories de la page d'index sont devenues cliquables
- Ajout d'une zone description à chaque catégorie
- Dans l'administration, modification des zones de filtres
	Dans le config.php, ajout de : define("REFERENCES_EXACT_SEARCH", true);
- Ajout d'un système de plugins
- Ajout de permissions de lecture sur les catégories

Notes :
	- Le module doit être mis à jour dans le gestionnaire de modules de Xoops et il faut aller au moins une fois dans
	l'administration du module
	- Il faut mettre à jour le plugin pour sitemap (recopie) et RSSFit
	xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	x Vous devez mettre à jour les permissions de visualisation de chaque catégorie x
	xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

*******
v 1.6
*******
- Dans le menu Xoops, le module n'affiche plus un lien vers les flux RSS s'ils sont désactivés dans les préférences du module
- Amélioration de la compatibilité avec Xoops 2.4

*******
v 1.5
*******
- Dans les références, ajout d'une notion de poids
- Ajout de 2 options permettant de choisir sur quel critère trier les articles (date ou poids) et le sens de tri (ascendant ou descendant)

Note :
	Le module doit être mis à jour dans le gestionnaire de modules de Xoops et il faut aller au moins une fois
	dans les préférences du module

*******
v 1.4
*******
- Ajout de index.html là où il manquait
- La zone "Date manuelle" n'est plus obligatoire (dans l'admin lors de la création d'une référence)
- Correction de bugs dans les blocs de référence aléatoire et de dernières références
- Dans le formulaire de création d'une référence, ajout du numéro de l'image
- Correction d'un bug sur la page d'index dans le cas où plusieurs catégories avaient le même poids
- Ajout de la possibilité de filtrer les références dans l'administration du module
Note :
	Le module doit être mis à jour dans le gestionnaire de modules de Xoops

*******
v 1.3
*******
- Dans les préférences, ajout de 2 options pour redimensionner l'image principale de chaque référence
- changelog.txt est devenu changelog.php
- Le module doit être mis à jour dans le gestionnaire de modules de Xoops

*******
v 1.2
*******
- Les blocs sont maintenant en mesure d'afficher la référence cliquée
- Le module, sur sa page d'accueil ouvre maintenant, par défaut, la première référence
- Dans config.php, ajout d'une option, REFERENCES_AUTO_FILL_MANUAL_DATE, qui permet de remplir automatiquement la date manuelle lorsqu'on crée une référence (avec la date du jour)
- Dans l'administration il est maintenant possible de passer un article en ligne ou hors ligne en cliquant directement sur l'ampoule

*******
v 1.1
*******
- Correction d'un bug dans l'administration lors de la suppression d'une référence

*******
v 1.0
*******
- Initial release