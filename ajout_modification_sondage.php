<?php 
require_once 'lib/required_files.php';
require_once 'lib/poll.php';
require_once 'lib/category.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php');;
}
require_once 'templates/header.php';


$categories = getCategories($pdo);

$pollError = null;
$pollMessage = null;
$itemError = null;
$itemMessage = null;

$poll = [
    'title' => '',
    'description' => '',
    'category_id' => '',
];
$item = [
    'id' => null,
    'name' => '',
];

if (isset($_POST['savePoll'])) {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $id = null;
    }

    $res = savePoll($pdo, $_POST['title'], $_POST['description'], $_POST['category_id'],(int)$_SESSION['user']['id'], $id);
    if ($res) {
        header('Location: ajout_modification_sondage.php?id='.$res);
    } else {
        $pollErrors = 'Une erreur est survenue lors de l\'enregistrement du sondage';
    }
}

if (isset($_POST['saveItem'])) {
    if(empty($_POST['name'])) {
        $itemError = "le nom ne doit pas ètre vide";
    } else {
        $res = savePollItem($pdo, (int)$_GET['id'], $_POST['name'], (int)$_POST['id']);
        if ($res) {
            $itemMessage = "Proposition bien enregistée";
        } else {
            $itemError = "Proposition non sauvegardée";
        }
    }
} else {
    if (isset($_GET['item_id']) && isset($_GET['action'])) {
        if ($_GET['action'] === 'edit') {
            $item = getPollItemById($pdo, $_GET['item_id']);

        } else if ($_GET['action'] === 'delete') {
            $res = deletePollItemById($pdo, $_GET['item_id']);
            if ($res) {
                $itemMessage = "Proposition supprimée";
            } else {
                $itemError = 'Erreur pendant la suppression';
            }
        }
    }
}


if (isset($_GET['id'])) {
    $poll = getPollById($pdo, (int)$_GET['id']);

    if ((int)$poll['user_id'] !== (int)$_SESSION['user']['id']) {
        header('Location: sondages.php');
    }
    $items = getPollItems($pdo, (int)$_GET['id']);
}

?>

<h1>Ajout / Modification d'un sondage</h1>

<?php if ($pollMessage) { ?>
    <div class="alert alert-success">
        <?= $pollMessage; ?>
    </div>   
<?php } ?>

<?php if ($pollError) { ?>
    <div class="alert alert-success">
        <?= $pollError; ?>
    </div>   
<?php } ?>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Titre</label>
        <input type="text" name="title" id="title" class="form-control" value="<?=$poll['title'] ?>">
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" cols="30" row="10" class="form-control"><?=$poll['description']?></textarea>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label" >Catégorie</label>
        <select name="category_id" id="category_id" class="form-select">
            <?php foreach ($categories as $category) { ?>
                <option <?php if ($poll['category_id'] == $category['id']) { echo 'selected="selected"';}?> value="<?=$category['id']?>"><?=$category['name']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="mb-3">
        <input type="submit" value="Enregister" name="savePoll"  class="btn btn-primary" >
    </div>
</form>

<?php if(!isset($_GET['id'])) { ?>
    <div class="alert alert-warning" role=""alert>
        Après avoir enregistré votre sondage, vous pourrez ajouter des propositions
    </div>
<?php } else { ?>
    <div class="row m-4">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $pollItem) { ?>
                    <tr>
                        <td><?= $pollItem['name'] ?></td>
                        <td><a href="ajout_modification_sondage.php?id=<?= $_GET['id'] ?>&item_id=<?= $pollItem['id'] ?>&action=edit">Modifier</a>
                            <a href="ajout_modification_sondage.php?id=<?= $_GET['id'] ?>&item_id=<?=$pollItem['id']?>&action=delete" onclick="return confirm('Etes-vous sur de supprimer')">Supprimer</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($itemMessage) { ?>
        <div class="alert alert-success">
            <?= $itemMessage ?>
        </div>
    <?php } ?>


    <?php if ($itemError) { ?>
        <div class="alert alert-success">
            <?= $itemError ?>
        </div>
    <?php } ?>

<div class="row m-4">
<form method="post" class="border">
    <h3>Proposition</h3>

    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input type="text" name="name" id="name" class="form-control" value="<?=$item['name']?>">
    </div>
        <input type="hidden" value="<?= $item['id']?>" name="id">
    <div class="mb-3">
        <input type="submit" name="saveItem" class="btn btn-primary" value="Enregistrer">
    </div>    
</form>
</div>

<?php } ?>


<?php require_once 'templates/footer.php'; ?>