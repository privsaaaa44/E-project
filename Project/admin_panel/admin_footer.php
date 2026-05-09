        </main>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sidebarToggle = document.getElementById('sidebarCollapse');
            var sidebar = document.getElementById('sidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
