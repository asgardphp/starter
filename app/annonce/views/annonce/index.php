			<div id="main">
				<div id="content">
					<h1>Petites annonces</h1>
					
					<hr class="small"/>
					
					<div style="width:500px; color:#636466">
						<p>Vous souhaitez annoncer un concert ? Vous organisez un stage ou vous êtes un chœur en recherche d'un chef ou de choristes ?</p>
						<p>Cette page de petites annonces est destiné à vous aider dans votre communication et vos recherches.</p>
					</div>
					
					<form action="annonces" method="post">
					<select class="dark" name="categorie">
						<option value="">Choisir</option>
						<option value="Stage">Stage</option>
						<option value="Chef de choeur">Chef de choeur</option>
						<option value="Projet">Projet</option>
						<option value="Choriste">Choriste</option>
						<option value="Concert">Concert</option>
					</select> 
					<select class="dark" name="region">
						<option value="">Choisir</option>
						<?php foreach(Arpa::$regions as $r): ?>
						<option value="<?php echo $r ?>"><?php echo $r ?></option>
						<?php endforeach ?>
					</select>
					<a class="button" style="float:right; margin:0px 30px 0 0;" href="annonces/depot">Proposer<br/>une annonce</a> <input style="float:right;margin-right:5px;" type="image" src="images/recherche/recherche.png" alt="Lancer la recherche"/>
					</form>
					<br/><br/><br/><br/>
					
					<table>
						<thead>
							<tr>
								<td>Date</td>
								<td>Intitulé</td>
								<td>Catégorie</td>
								<td>Ville</td>
							</tr>
						</thead>
						<tbody>
							<?php foreach($annonces as $annonce): ?>
							<tr>
								<td class="center"><?php echo $annonce->created_at->format('d/m/y') ?></td>
								<td><a href="annonces/<?php echo $annonce->id ?>/<?php echo $annonce->slug ?>"><?php echo $annonce ?></a></td>
								<td><?php echo $annonce->categorie ?></td>
								<td class="center"><?php echo $annonce->ville ?></td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>
					
					<?php $paginator->display('annonces') ?>
				</div>
				<?php $this->component('default', 'sidebar') ?>
			</div>