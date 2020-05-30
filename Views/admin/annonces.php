<table class="table table-striped">
    <thead>
        <th>ID</th>
        <th>Titre</th>
        <th>Contenu</th>
        <th>Actif</th>
        <th>Actions</th>
    </thead>
    <tbody>
        <?php foreach($annonces as $annonce): ?>
            <tr>
                <td><?= $annonce->id ?></td>
                <td><?= $annonce->titre ?></td>
                <td><?= $annonce->description ?></td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch<?= $annonce->id ?>" <?= $annonce->actif ? 'checked' : '' ?> data-id="<?= $annonce->id ?>">
                        <label class="custom-control-label" for="customSwitch<?= $annonce->id ?>"></label>
                    </div>    
                </td>
                <td>
                    <a href="/annonces/modifier/<?= $annonce->id ?>" class="btn btn-warning">Modifier</a><a href="/admin/supprimeAnnonce/<?= $annonce->id ?>" class="btn btn-danger">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>