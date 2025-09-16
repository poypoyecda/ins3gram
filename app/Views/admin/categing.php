<div class="row">
    <div class="col-md-3">
        <div class="card">
            <?= form_open('admin/categ_ing/insert') ?>
            <div class="card-header h4">
                Créer une catégorie d'ingrédient
            </div>
            <div class="card-body">
                <div class="form-floating mb-3">
                    <input id="name" class="form-control" placeholder="Nom de la catégorie" type="text" name="name" required>
                    <label for="name">Nom de la catégorie</label>
                </div>
                <div class="form-floating">
                    <select id="id_categ_parent" class="form-select" name="id_categ_parent">
                        <option value="">Aucune catégorie parent</option>
                        <!-- Options seront chargées via AJAX -->
                    </select>
                    <label for="id_categ_parent">Catégorie parent (optionnel)</label>
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Créer la catégorie</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header h4">
                Liste des catégories d'ingrédients
            </div>
            <div class="card-body">
                <table id="categIngTable" class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie parent</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalCategIng" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Éditer la catégorie d'ingrédient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="modalNameInput" placeholder="Nom de la catégorie" data-id="">
                    <label for="modalNameInput">Nom de la catégorie</label>
                </div>
                <div class="form-floating">
                    <select id="modalCategParentSelect" class="form-select">
                        <option value="">Aucune catégorie parent</option>
                        <!-- Options seront chargées dynamiquement -->
                    </select>
                    <label for="modalCategParentSelect">Catégorie parent (optionnel)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                <button onclick="saveCategIng()" type="button" class="btn btn-primary">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var baseUrl = "<?= base_url(); ?>";
        var table = $('#categIngTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('datatable/searchdatatable') ?>',
                type: 'POST',
                data: {
                    model: 'CategIngModel'
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                {
                    data: 'parent_name',
                    render: function(data, type, row) {
                        if (data && data !== null && data !== '') {
                            return data;
                        } else {
                            return '<span class="text-muted">Aucune</span>';
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        var parentId = row.id_categ_parent ? row.id_categ_parent : '';
                        var parentName = row.parent_name ? row.parent_name : '';
                        return `
                            <div class="btn-group" role="group">
                                <button onclick="showModal(${row.id},'${escapeString(row.name)}','${parentId}','${escapeString(parentName)}')"  class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategIng(${row.id})" class="btn btn-sm btn-danger" title="Supprimer">
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

        // Charger les options de catégories parentes pour la création
        loadCategories();
    });

    const myModal = new bootstrap.Modal('#modalCategIng');

    // Fonction pour échapper les chaînes dans les attributs HTML
    function escapeString(str) {
        if (!str) return '';
        return str.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    }

    // Charger toutes les catégories sauf celle en cours d'édition
    function loadCategories(excludeId = null) {
        $.ajax({
            url: '<?= base_url('/admin/categing/getCategories') ?>',
            type: 'POST',
            data: {
                exclude_id: excludeId
            },
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Aucune catégorie parent</option>';
                    response.data.forEach(function(categ) {
                        options += `<option value="${categ.id}">${categ.name}</option>`;
                    });

                    // Si c'est pour le formulaire de création
                    if (excludeId === null) {
                        $('#id_categ_parent').html(options);
                    }
                    // Si c'est pour le modal d'édition
                    $('#modalCategParentSelect').html(options);
                }
            },
            error: function() {
                console.log('Erreur lors du chargement des catégories');
            }
        });
    }

    function showModal(id, name, parentId, parentName) {
        $('#modalNameInput').val(name);
        $('#modalNameInput').data('id', id);

        // Charger les catégories en excluant l'ID actuel
        loadCategories(id);

        // Attendre que les options soient chargées puis sélectionner la valeur
        setTimeout(function() {
            $('#modalCategParentSelect').val(parentId || '');
        }, 100);

        myModal.show();
    }

    function saveCategIng() {
        let name = $('#modalNameInput').val().trim();
        let id = $('#modalNameInput').data('id');
        let parentId = $('#modalCategParentSelect').val();

        // Validation
        if (!name) {
            Swal.fire({
                title: 'Erreur !',
                text: 'Le nom de la catégorie est obligatoire',
                icon: 'error'
            });
            return;
        }

        // Vérifier qu'on ne sélectionne pas son propre ID comme parent
        if (parentId && parseInt(parentId) === parseInt(id)) {
            Swal.fire({
                title: 'Erreur !',
                text: 'Une catégorie ne peut pas être son propre parent',
                icon: 'error'
            });
            return;
        }

        $.ajax({
            url: '<?= base_url('/admin/categing/update') ?>',
            type: 'POST',
            data: {
                name: name,
                id: id,
                id_categ_parent: parentId || null
            },
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
                    // Actualiser la table et recharger les catégories
                    refreshTable();
                    loadCategories();
                } else {
                    console.log(response.message)
                    Swal.fire({
                        title: 'Erreur !',
                        text: response.message || 'Une erreur est survenue',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                myModal.hide();
                Swal.fire({
                    title: 'Erreur !',
                    text: 'Erreur de communication avec le serveur',
                    icon: 'error'
                });
            }
        })
    }

    function deleteCategIng(id){
        Swal.fire({
            title: `Êtes-vous sûr ?`,
            text: `Voulez-vous vraiment supprimer cette catégorie d'ingrédient ?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: `Oui !`,
            cancelButtonText: "Annuler",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('/admin/categing/delete') ?>',
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
                            // Actualiser la table et recharger les catégories
                            refreshTable();
                            loadCategories();
                        } else {
                            console.log(response.message)
                            Swal.fire({
                                title: 'Erreur !',
                                text: response.message || 'Une erreur est survenue',
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
                })
            }
        });
    }
</script>