<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <a class="navbar-brand" href="index.php?page=home">Személyi edző</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=home">Kezdőlap</a>
                </li>

                <?php if(isset($_SESSION['Email'])): ?><!-- user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=search">Edzéstervek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php?page=profile">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Kijelentkezés</a>
                </li>
                <?php if($_SESSION['Rank'] == "trainer") : ?><!-- for trainers,    other auths: user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=execa">Gyakorlat létrehozása</a>
                </li>
                <?php elseif($_SESSION['Rank'] == "admin") : ?><!-- admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=execa">Edzés hozzáadása</a>
                </li>
                <?php endif; else : ?><!-- guest -->
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <a class="nav-link" role="button">Bejelentkezés</a>
                </li>
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <a class="nav-link" role="button">Regisztráció</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if(!isset($_SESSION['Email'])) : ?>
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Bejelentkezés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Megsem"></button>
            </div>
            <div class="modal-body">
                <form id="loginModalForm" action="login.php" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder="name@example.com">
                        <label for="floatingInput">Email cím</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Jelszo">
                        <label for="floatingPassword">Jelszó</label>
                    </div>
                    <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezar</button>
                <button type="submit" form="loginModalForm" class="btn btn-primary">Bejelentkezes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Regisztracio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezar"></button>
            </div>
            <div class="modal-body">
                <form id="registerModalForm" action="register.php" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="registerFirstName" name="registerFirstName" placeholder="Janos">
                        <label for="floatingInput">Név</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="registerLastName" name="registerLastName" placeholder="Dobo">
                        <label for="floatingInput">Vezetéknév</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder="dobo@cs.cs">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="registerPhone" name="registerPhone" placeholder="06066666666">
                        <label for="floatingInput">Telefonszám</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="registerPassword" name="registerPassword" placeholder="Jelszo">
                        <label for="floatingPassword">Jelszó</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="registerPasswordConfirm" name="registerPasswordConfirm" placeholder="Jelszo">
                        <label for="floatingPassword">Jelszó visszaigazolás</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="trainer" id="registerUserType" name="registerUserType">
                        <label class="form-check-label" for="flexCheckDefault">
                            Edző vagyok
                        </label>
                    </div>
                    <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezar</button>
                <button type="submit" form="registerModalForm" class="btn btn-primary">Regisztracio</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="scripts/forms.js"></script>
<?php endif; ?>