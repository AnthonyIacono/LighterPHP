<form action="<?= lighter()->get_request()->getUri() ?>" method="POST">
    <label>
        <input type="text" name="email" />
    </label>
    <label>
        <input type="password" name="password" />
    </label>
    <label>
        <input type="password" name="confirm-password" />
    </label>
    <input type="submit" value="Submit" />
</form>