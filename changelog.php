<?php exit(); ?>
*******
v 1.8
*******
- Ajout d'une zone permettant d'afficher du texte compl�mentaire sur la page de d�tail d'une r�f�rence
- Dans l'administration, ajout d'un lien permettant d'aller directement voir les blocs du module
- Dans les blocs (r�f�rences r�centes et r�f�rences au hasard), possibilit� de choisir les cat�gories � afficher
- Ajout de 2 pr�f�rences pour choisir la zone de tri par d�faut dans la liste des r�f�rences dans l'admin
- Dans la liste des r�f�rences, dans l'admin, ajout de la colonne "poids"
- Modification du module pour qu'il puisse �tre utilis�e par d'autres modules 

Notes :
- Le module doit �tre mis � jour et il faut r�gler les pr�f�rences du module


*******
v 1.72
*******
- Mise � jour de la librairie utilis�e pour redimensionner les images


*******
v 1.71
*******
- Correction d'un bug dans la visualisation des cat�gories (plus aucune cat�gorie visible)
- Correction d'un bug lors de la maintenance des tables


*******
v 1.7
*******
- Le module n�cessite Xoops >= 2.3.3
- Ajout d'une page qui liste les cat�gories (category.php => references_category.html)
- Ajout d'une page qui affiche le contenu d'une r�f�rence (reference.php => references_reference.html)
- Modifications du template references_index.html (ajout d'une liste d�roulante des cat�gories)
- Modification de la recherche pour que le lien pointe vers la page d'une r�f�rence
- Modification du plugin sitemap
- Ajout de tests pour v�rifier la pr�sence du module TAG
- Ajout d'une notification sur la cr�ation d'une cat�gorie
- Dans l'administration des cat�gories, le titre de chaque cat�gorie est cliquable
- Dans les pages c�t� utilisateur, ajout de liens vers l'administration
- C�t� utilisateur, les cat�gories de la page d'index sont devenues cliquables
- Ajout d'une zone description � chaque cat�gorie
- Dans l'administration, modification des zones de filtres
	Dans le config.php, ajout de : define("REFERENCES_EXACT_SEARCH", true);
- Ajout d'un syst�me de plugins
- Ajout de permissions de lecture sur les cat�gories

Notes :
	- Le module doit �tre mis � jour dans le gestionnaire de modules de Xoops et il faut aller au moins une fois dans
	l'administration du module
	- Il faut mettre � jour le plugin pour sitemap (recopie) et RSSFit
	xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	x Vous devez mettre � jour les permissions de visualisation de chaque cat�gorie x
	xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

*******
v 1.6
*******
- Dans le menu Xoops, le module n'affiche plus un lien vers les flux RSS s'ils sont d�sactiv�s dans les pr�f�rences du module
- Am�lioration de la compatibilit� avec Xoops 2.4

*******
v 1.5
*******
- Dans les r�f�rences, ajout d'une notion de poids
- Ajout de 2 options permettant de choisir sur quel crit�re trier les articles (date ou poids) et le sens de tri (ascendant ou descendant)

Note :
	Le module doit �tre mis � jour dans le gestionnaire de modules de Xoops et il faut aller au moins une fois
	dans les pr�f�rences du module

*******
v 1.4
*******
- Ajout de index.html l� o� il manquait
- La zone "Date manuelle" n'est plus obligatoire (dans l'admin lors de la cr�ation d'une r�f�rence)
- Correction de bugs dans les blocs de r�f�rence al�atoire et de derni�res r�f�rences
- Dans le formulaire de cr�ation d'une r�f�rence, ajout du num�ro de l'image
- Correction d'un bug sur la page d'index dans le cas o� plusieurs cat�gories avaient le m�me poids
- Ajout de la possibilit� de filtrer les r�f�rences dans l'administration du module
Note :
	Le module doit �tre mis � jour dans le gestionnaire de modules de Xoops

*******
v 1.3
*******
- Dans les pr�f�rences, ajout de 2 options pour redimensionner l'image principale de chaque r�f�rence
- changelog.txt est devenu changelog.php
- Le module doit �tre mis � jour dans le gestionnaire de modules de Xoops

*******
v 1.2
*******
- Les blocs sont maintenant en mesure d'afficher la r�f�rence cliqu�e
- Le module, sur sa page d'accueil ouvre maintenant, par d�faut, la premi�re r�f�rence
- Dans config.php, ajout d'une option, REFERENCES_AUTO_FILL_MANUAL_DATE, qui permet de remplir automatiquement la date manuelle lorsqu'on cr�e une r�f�rence (avec la date du jour)
- Dans l'administration il est maintenant possible de passer un article en ligne ou hors ligne en cliquant directement sur l'ampoule

*******
v 1.1
*******
- Correction d'un bug dans l'administration lors de la suppression d'une r�f�rence

*******
v 1.0
*******
- Initial release