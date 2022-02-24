<script src="assets/js/ajax-form.js" type="module"></script>
<noscript> Для работы с данным сайтом необходим JavaScript </noscript>
<form id="login-form" data-action="./index.php" data-onsuccess-method="reload" method="POST">
    <input
        type="hidden"
        name="action"
        value="login"
        >
    <label for="login-form-login">login:</label>
    <input
        type="text"
        id ="login-form-login"
        name="login"
        >
    <div id ="login-form-login-error" class="error-message"></div>
    <br>
    <label for="login-form-password">password:</label>
    <input
        type="password"
        id="login-form-password"
        name="password"
        >
    <div id="login-form-password-error" class="error-message"></div>
    <br>
    <input type="submit">
</form>