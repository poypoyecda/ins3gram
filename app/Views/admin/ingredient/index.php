<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Liste des ingrédients</h3>
                <a href="<?= base_url("admin/ingredient/new") ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Ingrédient
                </a>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered table-striped" id="tableIngredients">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Marque</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var baseUrl = "<?= base_url(); ?>";
        var table = $('#tableIngredients').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('datatable/searchdatatable') ?>',
                type: 'POST',
                data: {
                    model: 'IngredientModel'
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'categname'},
                { data: 'brandname'},
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="<?= base_url('admin/ingredient/') ?>${row.id}") class="btn btn-sm btn-warning text-white" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteIngredient(${row.id})" class="btn btn-sm btn-danger text-white" title="Supprimer">
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
    });
</script>