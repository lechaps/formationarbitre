<?php
/* D E F I N I T I O N   D E S   D R O I T S   Q U E S T I O N N A I R E S */
if (in_array($_SESSION['Tright'],$T_right["0"]["UPGRADABLE"])) {
	$T_creationLink	='<li><a href="T_form.php"><span class="glyphicon glyphicon-pencil">&nbsp;</span>Saisir un nouveau questionnaire</a></li>';
}
else
{
	$T_creationLink	='';
}
/* D E F I N I T I O N   D E S   D R O I T S   Q U E S T I O N S */
if (in_array($_SESSION['Qright'],$Q_right["0"]["UPGRADABLE"])) {
	$Q_creationLink	='<li><a href="Q_form.php"><span class="glyphicon glyphicon-pencil">&nbsp;</span>Saisir une nouvelle question</span></a></li>';
}
else
{
	$Q_creationLink	='';
} ?>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php"><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo $appli_name;?></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-list-alt">&nbsp;</span>Question&nbsp;<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="Q_search.php"><span class="glyphicon glyphicon-search">&nbsp;</span>Rechercher une question</a></li>
						<?php echo $Q_creationLink;?>
					</ul>
				</li>
			</ul>
			<ul class="nav navbar-nav">
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-book">&nbsp;</span>Questionnaire&nbsp;<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="T_search.php"><span class="glyphicon glyphicon-search">&nbsp;</span>Rechercher un questionnaire </a></li>
						<?php echo $T_creationLink;?>
					</ul>
				</li>
			</ul>
			<?php if (in_array($_SESSION['Uright'],$U_right["FULLADMIN"]))  { ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-cog">&nbsp;</span>Administration&nbsp;<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="U_search.php"><span class="glyphicon glyphicon-education">&nbsp;</span>Gérer les utilisateurs</a></li>
							<li><a href="A_params.php"><span class="glyphicon glyphicon-scale">&nbsp;</span>Gérer les paramètres techniques</a></li>
							<li class="divider"></li>
							<li><a href="A_BDDUpdate.php"><span class="glyphicon glyphicon-cd">&nbsp;</span>Mise à jour des contraintes de BDD</a></li>
							<li><a href="A_UserEvent.php"><span class="glyphicon glyphicon-sunglasses">&nbsp;</span>Log</a></li>
						</ul>
					</li>
				</ul>
			<?php   }
			if (isset($_SESSION['idUtilisateur'] ))   { ?>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-user">&nbsp;</span>Utilisateur&nbsp;<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="U_profil.php"><span class="glyphicon glyphicon-credit-card">&nbsp;</span>Profil</a></li>
							<li><a href="U_deconnexion.php"><span class="glyphicon glyphicon-transfer">&nbsp;</span>Se déconnecter</a></li>
						</ul>
					</li>
				</ul>
			<?php } else { ?>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="U_connexion.php"> <span class="glyphicon glyphicon-user">&nbsp;</span>Administration</a></li>
				</ul>
			<?php } ?>
		</div>
	</div>
</div>