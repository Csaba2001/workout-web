<nav id="nav" class="navbar sticky-top navbar-dark navbar-expand-lg bg-dark shadow">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <a class="navbar-brand" href="index.php?page=home">Személyi edző</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if($user): ?><!-- user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=workout">Edzéstervek</a>
                </li>
                <?php if($user->isUser()) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=search">Kereső</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=trainers">Értékelés</a>
                </li>
                <?php elseif($user->isTrainer()) : ?><!-- for trainers,    other auths: user, trainer, admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=execa">Gyakorlatok</a>
                </li>
                <?php elseif($user->isAdmin()) : ?><!-- admin -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=categories">Edzéskategóriák</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=users">Felhasználók</a>
                </li>
                <?php endif; else : ?><!-- guest -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=home">Kezdőlap</a>
                </li>
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <a class="nav-link" role="button">Bejelentkezés</a>
                </li>
                <li class="nav-item" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <a class="nav-link" role="button">Regisztráció</a>
                </li>
                <?php endif; ?>
            </ul>
            <?php if($user): ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $user->displayName() ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
                        <li><span class="text-muted dropdown-item">
                            <?php
                            switch($user->Rank){
                                case "user":
                                    echo "Felhasználó";
                                    break;
                                case "trainer":
                                    echo "Edző";
                                    break;
                                default:
                                    echo "Admin";
                                    break;
                            }
                            ?>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?page=profile">Profil</a></li>
                        <li><a class="dropdown-item" href="login.php">Kijelentkezés</a></li>
                    </ul>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php if(!$user) : ?>
<div class="modal fade" id="forgotPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Elfelejtett jelszó</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Megsem"></button>
            </div>
            <div class="modal-body">
                <form ajax id="forgotPasswordModalForm" action="forgot.php" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Email cím">
                        <label for="Email">Email cím</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="Hash" name="Hash" placeholder="Új jelszó">
                        <label for="Hash">Új jelszó</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="PasswordConfirm" name="PasswordConfirm" placeholder="Új jelszó visszaigazolás">
                        <label for="PasswordConfirm">Új jelszó visszaigazolás</label>
                    </div>
                    <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#loginModal">Nem felejtettem el</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                <button type="submit" form="forgotPasswordModalForm" class="btn btn-primary">Küldés</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Bejelentkezés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Megsem"></button>
            </div>
            <div class="modal-body">
                <form ajax id="loginModalForm" action="login.php" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="Email" name="Email" placeholder="name@example.com">
                        <label for="floatingInput">Email cím</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="Hash" name="Hash" placeholder="Jelszo">
                        <label for="floatingPassword">Jelszó</label>
                    </div>
                    <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Elfelejtett jelszó</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                <button type="submit" form="loginModalForm" class="btn btn-primary">Bejelentkezés</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Regisztráció</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezár"></button>
            </div>
            <div class="modal-body">
                <form ajax id="registerModalForm" action="register.php" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="FirstName" name="FirstName" placeholder="Név">
                        <label for="FirstName">Név</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="LastName" name="LastName" placeholder="Családnév">
                        <label for="LastName">Családnév</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Email">
                        <label for="Email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="Phone" name="Phone" placeholder="Telefonszám">
                        <label for="Phone">Telefonszám</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="Hash" name="Hash" placeholder="Jelszó">
                        <label for="Hash">Jelszó</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="PasswordConfirm" name="PasswordConfirm" placeholder="Jelszó visszaigazolás">
                        <label for="PasswordConfirm">Jelszó visszaigazolás</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="trainer" id="registerUserType" name="registerUserType" onclick="trainerInput(this, this.form)">
                        <label class="form-check-label" for="flexCheckDefault">
                            Edző vagyok
                        </label>
                    </div>
                    <div id="cvInput" class="form-floating mb-3" style="display: none;">
                        <textarea class="form-control" name="CV" id="CV" disabled></textarea>
                        <label for="CV">CV</label>
                    </div>
                    <div class="alert alert-danger mt-2" role="alert" style="display: none;">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégsem</button>
                <button type="submit" form="registerModalForm" class="btn btn-primary">Regisztráció</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="scripts/forms.js"></script>
<?php endif; ?>