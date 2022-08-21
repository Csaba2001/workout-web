<?php
@session_start();
require_once("db_config.php");
include_once("functions.php");

$trainers = getTrainers();
?>
<div class="container-fluid p-0">
    <?php if(!$user): ?>
    <div class="container-fluid d-flex flex-row col-lg-6 mb-4 p-2">
        <div class="d-flex flex-column">
            <div class="d-flex flex-row justify-content-between my-2">
                <h2>Eleged van ebből?</h2>
                <a data-bs-toggle="modal" data-bs-target="#registerModal" role="button" class="link-primary h2 align-self-center">Regisztrálj!</a>
            </div>
            <img class="img-fluid" src="images/man-641691_1920.jpg" alt="Pickle man">
        </div>
    </div>

    <h3 class="container h2 my-3 text-center">CÉLJAIDNAK MEGFELELŐEN VÁLASZTHATSZ EGYÉNI, VAGY PÁROS EDZÉSEK KÖZÜL:</h3>
    <h4 class="h3 mb-5 text-center">1/1, egyéni személyi edzés</h4>

    <div class="container d-flex flex-column">
        <p class="text-center">
            Először, egy szakképzett edző konzultál Veled, ahol megismerjük az egészségi állapotodat, múltbeli sérüléseidet, edzéstörténetedet a konkrét céljaiddal együtt. Megtudhatod, hogyan jutsz el a fitness céljaidhoz.
            Mindez, azért jár Neked, mert klubunk vendége vagy és nem jár semmilyen kötelezettséggel. Foglalj konzultációs időpontot a lentebb lévő gombra kattintva, az általad választott edzőnél!
            <br><br>
            A második alkalommal egy komplett felmérésen esel át. Egy funkcionális mozgásminta szűréssel képet kapunk a mozgásodban rejlő kockázatokról. Elemezzük a testtartásod, valamint egy Tanita teljes testanalízistvégzünk, amivel egy objektív képet kapsz a tested aktuális testzsírszázalékáról, izomtömegéről, vízháztartásáról, napi kalória szükségletéről és alapanyagcseréjéről.
            <br><br>
            A következő alkalommal pedig már elkezdheted a Rád szabott fitness programot, ami edzésről edzésre közelebb visz céljaidhoz. Mindenki konkrét célokkal és egy meghatározott testtel rendelkezik. A testre szabott eredményekhez személyre szabott programra van szükség.
        </p>
        <h4 class="h3 mb-3 mt-4 text-center">Páros személyi edzés</h4>
        <p class="text-center">
            Ha szeretnél, egy személyre szabott edzésprogramot, valamint szívesen eddzenél együtt más emberekkel a céljaid felé vezető úton, illetve ha egy edző társat keresel, akivel kölcsönösen tudjátok motiválni egymást. Edzéseitek energikusabbak lesznek, megtaláljátok benne a kihívásokat és jól érzitek magatokat.
        </p>
    </div>
    <?php endif; ?>
    <div class="container d-flex flex-column col-lg-12">
        <h4 class="h1 mb-3 mt-5">Edzőink</h4>
        <?php foreach($trainers as $trainer): ?>
        <?php if($trainer["approval"] === "approved"): ?>
        <div class="row mb-4 text-break">
            <div class="d-flex flex-column col-lg-8 align-items-end">
                <h5><?= substr($trainer["LastName"],0,1).". ".$trainer["FirstName"] ?></h5>
                <p class="text-justify"><?= substr($trainer["CV"],0,800); if(strlen($trainer["CV"]) >= 800){ echo "..."; } ?></p>
                <?php if($trainer["rating"]): ?>
                <p class="fs-6 text-muted ms-2">Értékelések száma: <?= $trainer["rated"] ?></p>
                <div class="d-flex flex-row flex-wrap-0 mb-2">
                    <?php for($i = 0; $i < $trainer["rating"]; $i++): ?>
                    <span class="material-symbols-outlined text-warning">
                    star
                    </span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if($trainer["picture"]): ?>
            <div class="col-lg-4">
                <img class="img-fluid mb-2" alt="<?= $trainer["FirstName"] ?>" src="images/<?= $trainer["picture"] ?>">
            </div>
            <?php endif; ?>
            <hr class="mb-5 mt-3">
        </div>
        <?php endif; endforeach; ?>
    </div>

    <footer class="p-4 bg-dark">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="#nav" class="nav-link px-2">Vissza az oldal tetejére</a></li>
            <li class="nav-item"><a target="_blank" href="https://github.com/Csaba2001" class="nav-link px-2">Github</a></li>
            <li class="nav-item"><a target="_blank" href="https://pixabay.com/" class="nav-link px-2">Pixabay</a></li>
            <li class="nav-item"><a target="_blank" href="https://getbootstrap.com/" class="nav-link px-2">Bootstrap</a></li>
            <li class="nav-item"><a target="_blank" href="https://fonts.google.com/icons" class="nav-link px-2">Google Fonts</a></li>
        </ul>
        <p class="text-center text-muted">Ez az oldal egy tanulói projektmunka, az oldalon megjelenő személyek nem léteznek</p>
        <p class="text-center text-muted">© <?= date("Y") ?> Dobó Csaba</p>
    </footer>

</div>