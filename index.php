    <?php include 'header.php'; ?>

    <!-- Content -->
    <div class="content">
        <h2>Welcome, <?php echo ($username === 'admin') ? 'Administrator' : $username; ?>!</h2>
        <p>Select a section from the sidebar to manage your supply chain.</p>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function toggleSidebar() {
            let sidebar = document.querySelector('.sidebar');
            let content = document.querySelector('.content');
            sidebar.classList.toggle('open');
            content.classList.toggle('shift');
        }
    </script>

</body>
</html>

