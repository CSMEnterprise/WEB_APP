</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> NerdVault - Marketplace per prodotti nerd, gaming e collezionismo.</p>
    </div>
</footer>

<script>
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);

    if (!input) {
        return;
    }

    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = 'Nascondi';
    } else {
        input.type = 'password';
        button.textContent = 'Mostra';
    }
}
</script>

</body>
</html>