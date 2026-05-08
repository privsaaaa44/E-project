        </main>
    </div>

    <!-- Scripts (Local for Offline) -->
    <script src="../js/lib/jquery.min.js"></script>
    <script src="../js/lib/bootstrap.bundle.min.js"></script>
    <script src="../js/lib/select2.min.js"></script>
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
