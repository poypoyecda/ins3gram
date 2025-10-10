<?php
echo form_open_multipart("/admin/ingredient/insert"); ?>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <?php if(isset($user)) : ?>
                    <!-- Si l'utilisateur existe déjà : on affiche "Modification" -->
                    Modification de <?= esc($user->username); ?>
                <?php else : ?>
                    <!-- Sinon : on affiche "Création d'un utilisateur" -->
                    Création d'un utilisateur
                <?php endif;?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <input type="text" name="name" placeholder="Nom" class="form-control">
                        <input type="text" name="id_categ" value="1" class="form-control">

                        <input type="file" name="image[]" multiple id="image">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</div>


<?php
    echo form_close();
?>