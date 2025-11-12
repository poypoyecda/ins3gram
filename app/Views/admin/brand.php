<div class="row">
    <div class="col-md-3">
        <div class="card">
        <?= form_open_multipart('admin/brand/insert') ?>
            <div class="card-header h4">
                Créer une marque
            </div>
            <div class="card-body">
                <div class="form-floating">
                    <input id="name" class="form-control" placeholder="Nom de la marque" type="text" name="name" required>
                    <label for="name">Nom de la marque</label>
                </div>
                <div class="mt-3">
                    <input type="file" class="form-control" name="image">
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Créer la marque</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header h4">
                Liste des marques
            </div>
            <div class="card-body">
                <table id="brandTable" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Img</th>
                            <th>Nom</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modalBrand" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Éditer la marque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating">
                    <input type="text" class="form-control" id="modalNameInput" placeholder="Nom de la marque" data-id="">
                    <label for="modalNameInput">Nom de la marque</label>
                </div>
                <div class="mt-3 d-flex align-items-center">
                    <input type="file" class="form-control me-2" id="modalImageInput" accept="image/*" style="max-width: 70%;">
                    <img id="modalImagePreview" src="<?= base_url('assets/img/no-img.png') ?>" alt="Aperçu"
                         style="height: 60px; border: 1px solid #ccc; border-radius: 4px; object-fit: cover;">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                <button onclick="saveBrand()" type="button" class="btn btn-primary">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var baseUrl = "<?= base_url(); ?>";
        var table = $('#brandTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('datatable/searchdatatable') ?>',
                type: 'POST',
                data: {
                    model: 'BrandModel'
                }
            },
            columns: [
                { data: 'id' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        if(row.image_url) {
                            return `<img style="height:30px;" src='${baseUrl}/${row.image_url}'>
                        `;
                        } else {
                            return `<img style="height:30px;" src='${baseUrl}/assets/img/no-img.png'>
                        `;
                        }
                    }
                },
                { data: 'name' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                       return `
                            <div class="btn-group" role="group">
                                <button onclick="showModal(${row.id},'${row.name}')"  class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteBrand(${row.id})" class="btn btn-sm btn-danger" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                url: baseUrl + 'js/datatable/datatable-2.1.4-fr-FR.json',
            }
        });

        // Fonction pour actualiser la table
        window.refreshTable = function() {
            table.ajax.reload(null, false); // false pour garder la pagination
        };
        // Quand l'utilisateur sélectionne un fichier dans l'input "modalImageInput"
        $('#modalImageInput').on('change', function (e) {

            // On récupère le premier fichier sélectionné
            const file = e.target.files[0];

            // On sélectionne l'image d'aperçu dans le DOM
            const preview = $('#modalImagePreview');

            // Vérifie si un fichier a bien été choisi
            if (file) {

                // Vérifie que le fichier est bien une image (type MIME commence par "image/")
                if (file.type.startsWith('image/')) {

                    // On crée un objet FileReader : il permet de lire le contenu du fichier côté navigateur
                    const reader = new FileReader();

                    // Quand le fichier est chargé (événement onload), on met à jour la source de l'image d'aperçu
                    reader.onload = function (event) {
                        // event.target.result contient une version encodée en base64 de l’image
                        preview.attr('src', event.target.result);
                    };

                    // On lit le fichier comme une URL base64 pour l'afficher sans l'envoyer au serveur
                    reader.readAsDataURL(file);

                } else {
                    // Si le fichier n'est pas une image
                    Swal.fire({
                        icon: 'error',
                        title: 'Fichier non valide',
                        text: 'Veuillez sélectionner une image (JPG, PNG, etc.)'
                    });

                    // Réinitialise le champ file pour éviter qu’un fichier non valide reste sélectionné
                    e.target.value = '';

                    // Remet l’image d’aperçu par défaut
                    preview.attr('src', base_url + 'assets/img/no-img.png');
                }

            } else {
                // Aucun fichier sélectionné → on remet l’image par défaut
                preview.attr('src', base_url + 'assets/img/no-img.png');
            }
        });

    });

    const myModal = new bootstrap.Modal('#modalBrand');

    function showModal(id, name) {
        $('#modalNameInput').val(name);
        $('#modalNameInput').data('id', id);
        let table = $('#brandTable').DataTable();
        let data = table.data().toArray();
        let img_url;
        for (let item of data) {
            if (item.id == id) {
                console.log("Nom :", item.name);
                console.log("Image :", item.image_url);
                img_url = item.image_url;
                break;
            }
        }
        if (!img_url) {
            img_url = base_url + '/assets/img/no-img.png';
        }
        $('#modalImagePreview').attr('src', img_url);
        myModal.show();
    }

    function saveBrand() {
        let name = $('#modalNameInput').val();
        let id = $('#modalNameInput').data('id');
        let file = $('#modalImageInput')[0]?.files[0];

        //Utilisation de FormData au lieu d'un objet simple
        let formData = new FormData();
        formData.append('id', id);
        formData.append('name', name);
        if (file) formData.append('image', file); //ajoute l'image seulement si présente

        $.ajax({
            url: '<?= base_url('/admin/brand/update') ?>',
            type: 'POST',
            data: formData,
            processData: false,  //obligatoire pour FormData
            contentType: false,  //obligatoire pour FormData
            dataType: 'json',
            success: function(response) {
                myModal.hide();
                if (response.success) {
                    Swal.fire({
                        title: 'Succès !',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Actualiser la table
                    refreshTable();
                } else {
                    console.log(response.message)
                    Swal.fire({
                        title: 'Erreur !',
                        text: 'Une erreur est survenue',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Erreur !',
                    text: 'Erreur de communication avec le serveur',
                    icon: 'error'
                });
            }
        });
    }

    function deleteBrand(id){
        Swal.fire({
            title: `Êtes-vous sûr ?`,
            text: `Voulez-vous vraiment supprimer cette marque ?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: `Oui !`,
            cancelButtonText: "Annuler",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('/admin/brand/delete') ?>',
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Succès !',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Actualiser la table
                            refreshTable();
                        } else {
                            console.log(response.message)
                            Swal.fire({
                                title: 'Erreur !',
                                text: 'Une erreur est survenue',
                                icon: 'error'
                            });
                        }
                    }
                })
            }
        });
    }
</script>