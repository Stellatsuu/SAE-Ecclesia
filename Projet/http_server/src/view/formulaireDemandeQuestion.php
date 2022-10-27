 <main>
     <form method="post" action="../../../web/frontController.php?action=demanderCreationQuestion">
         <fieldset>
             <legend>Formulaire de demande de création d'une question :</legend>
             <p>
                 <label for="titre_id"><h3>Question :</h3></label>
                 <textarea rows=6 cols=50 id="titre_id" placeholder="Écrivez votre question ici" name="titre" required></textarea>
             </p>
             <p>
                 <label for="intitule_id"><h3>Intitulé :</h3></label>
                 <textarea rows=6 cols=50 id="intitule_id" placeholder="Écrivez les détails de votre question ici, la raison de cette demande, etc" name="intitule" required></textarea>
             </p>
             <p>
                 <label for="idUtilisateur_id">votre idUtilisateur (imaginez que c'est un login et un mot de passe)</label>
                 <input type="number" name="idUtilisateur" id="idUtilisateur_id" required />
             </p>
             <p>
                 <input type="submit" value="Envoyer" />
             </p>
         </fieldset>
     </form>
 </main>