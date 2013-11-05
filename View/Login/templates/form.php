<form action="<?php echo urlencode($Form->action) ?>" method="<?php echo $Form->method ?>" enctype="<?php echo $Form->enctype ?>">
    <input type="hidden" name="token" value="3">
    <table border="0">
        <tr>
            <td><Username</td>
            <td>:</td>
            <td><input type="text" value="" name="username" /></td>
        </tr>
        <tr>
            <td>Password</td>
            <td>:</td>
            <td><input type="password" name="password" /></td>
        </tr>
        <tr>
            <td colspan="3"><input type="submit"></td>
        </tr>
    </table>
</form>