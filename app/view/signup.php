<script src="assets/js/ajax-form.js" type="module"></script>
<noscript> Для работы с данным сайтом необходим JavaScript </noscript>
<form class="none" id="signup-form" data-action="./index.php" data-onsuccess-method="reload" method="POST">
    <input
        type="hidden"
        name="action"
        value="signup"
        >
    <label for="signup-form-login">login:</label>
    <input
        type="text"
        id ="signup-form-login"
        name="login"
        >
    <div id ="signup-form-login-error" class="error-message"></div>
    <br>
    <label for="signup-form-password">password:</label>
    <input
        type="password"
        id="signup-form-password"
        name="password"
        >
    <div id="signup-form-password-error" class="error-message"></div>
    <br>
    <label for="signup-form-confirm-password">confirm_password:</label>
    <input
        type="password"
        id="signup-form-confirm-password"
        name="confirm_password"
        >
    <div id="signup-form-confirm-password-error" class="error-message"></div>
    <br>
    <label for="signup-form-email">email:</label>
    <input
        type="text"
        id="signup-form-email"
        name="email"
        >
    <div id="signup-form-email-error" class="error-message"></div>
    <br>
    <label for="signup-form-name">name:</label>
    <input
        type="text"
        id="signup-form-name"
        name="name"
        >
    <div id="signup-form-name-error" class="error-message"></div>
    <br>
    <input type="submit">
</form>