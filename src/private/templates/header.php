<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header>
    <nav>
        <ul>
            <li><a href="register.php" class="<?= $current_page === 'register.php' ? 'active' : '' ?>">Register</a></li>
            <li><a href="login.php" class="<?= $current_page === 'login.php' ? 'active' : '' ?>">Login</a></li>
            <li><a href="profile.php" class="<?= $current_page === 'profile.php' ? 'active' : '' ?>">Profile</a></li>
        </ul>
    </nav>
</header>

<style>
    header {
        border-bottom: 1px solid black;
    }

    nav ul {
        margin: 0 auto;
        padding: 10px 0;
        display: flex;
        justify-content: space-between;
        width: 250px;
        list-style: none;
    }

    nav a {
        text-decoration: none;
    }

    nav a:visited {
        color: inherit;
    }

    .active {
        text-decoration: underline;
    }
</style>
