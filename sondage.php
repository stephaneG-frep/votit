<?php 
require_once 'templates/header.php';
require_once 'lib/poll.php';

$error404 = false;


if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $poll = getPollById($pdo, $id);

    if ($poll) {

        $results = getPollResultsByPollId($pdo, $id);
        var_dump($results);

    } else {
        $error404 = true;
    }

} else {
    $error404 = true;
}

if (!$error404) {
?>
<div class="row align-items-center g-5 py-5">
    <div class="col-lg-6">
    <h1 class="display-5fw-bold lh-1 mb-3"><?= $poll['title']?></h1>
    <p class="lead"><?=$poll['description'] ?></p>
        
    </div>
    <div class="col-10 col-sm-8 col-lg-6">
        <h2>Résultats</h2>
        <div class="results">
            <h3>Prop 1</h3>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-color-2"
                role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" 
                aria-valuemax="100">25% - prop 1
                </div>
            </div>

            <h3>Prop 2</h3>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-color-3"
                role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" 
                aria-valuemax="100">25% - prop 2
                </div>
            </div>

        </div>
    </div>
</div>
<?php } else { ?>
    <h1>Erreur 404</h1>
    <p style="color: crimson;font-weight: bold ">Le sondage demandé n'existe pas</p>
<?php } ?>

<?php require_once 'templates/footer.php'; ?>