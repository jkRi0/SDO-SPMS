    </div> <!-- /.container -->

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <!-- jQuery (required by DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all tables marked with .datatable
            document.querySelectorAll('table.datatable').forEach(function (tbl) {
                $(tbl).DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    columnDefs: [{ orderable: false, targets: -1 }],
                    // Default sort: Created column (index 5) descending
                    order: [[5, 'desc']],
                    language: { searchPlaceholder: "Search...", search: "" }
                });
            });

            // Auto-refresh supplier notifications in header bell
            var notifBadge = document.getElementById('notifBadge');
            var notifList = document.getElementById('notifList');
            if (notifBadge && notifList) {
                function refreshNotifications() {
                    if (document.visibilityState !== 'visible') {
                        return;
                    }

                    fetch('api_notifications.php', { cache: 'no-store' })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data || !data.success) return;

                            var unread = data.unread_count || 0;
                            if (unread > 0) {
                                notifBadge.style.display = 'inline-block';
                                notifBadge.textContent = unread > 9 ? '9+' : unread;
                            } else {
                                notifBadge.style.display = 'none';
                                notifBadge.textContent = '';
                            }

                            var items = data.notifications || [];

                            // Rebuild list body (keep the header item)
                            while (notifList.children.length > 1) {
                                notifList.removeChild(notifList.lastChild);
                            }

                            if (items.length === 0) {
                                var emptyLi = document.createElement('li');
                                emptyLi.className = 'px-3 py-2 small text-muted';
                                emptyLi.textContent = 'No notifications yet.';
                                notifList.appendChild(emptyLi);
                                return;
                            }

                            items.forEach(function (n) {
                                var li = document.createElement('li');
                                li.className = 'px-0 py-0 small';

                                var a = document.createElement('a');
                                a.href = n.link ? ('notification_open.php?id=' + encodeURIComponent(n.id)) : '#';
                                a.className = 'd-block px-3 py-2 text-reset text-decoration-none' + (n.is_read ? '' : ' fw-semibold notif-unread');

                                var topRow = document.createElement('div');
                                topRow.className = 'd-flex justify-content-between';

                                var spanTitle = document.createElement('span');
                                spanTitle.textContent = n.title || '';
                                var spanTime = document.createElement('span');
                                spanTime.className = 'text-muted';
                                spanTime.style.fontSize = '0.75rem';
                                spanTime.textContent = n.created_at || '';

                                topRow.appendChild(spanTitle);
                                topRow.appendChild(spanTime);

                                var msgDiv = document.createElement('div');
                                msgDiv.className = 'text-muted';
                                msgDiv.style.fontSize = '0.8rem';
                                msgDiv.textContent = n.message || '';

                                a.appendChild(topRow);
                                a.appendChild(msgDiv);

                                li.appendChild(a);
                                notifList.appendChild(li);
                            });
                        })
                        .catch(function () {
                            // ignore errors
                        });
                }

                // Initial load + interval
                refreshNotifications();
                setInterval(refreshNotifications, 5000);
            }

            // Auto-refresh admin feedback dropdown (admin role only)
            var adminFeedbackBadge = document.getElementById('adminFeedbackBadge');
            var adminFeedbackList = document.getElementById('adminFeedbackList');
            if (adminFeedbackBadge && adminFeedbackList) {
                function refreshAdminFeedback() {
                    if (document.visibilityState !== 'visible') {
                        return;
                    }

                    fetch('api_admin_feedback.php', { cache: 'no-store' })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data || !data.success) return;

                            var items = data.feedback || [];
                            var unread = data.unread || 0;

                            // Update badge (show unread count if > 0)
                            if (unread > 0) {
                                adminFeedbackBadge.style.display = 'inline-block';
                                adminFeedbackBadge.textContent = unread > 9 ? '9+' : unread;
                            } else {
                                adminFeedbackBadge.style.display = 'none';
                                adminFeedbackBadge.textContent = '';
                            }

                            // Rebuild list body (keep the header li at index 0)
                            while (adminFeedbackList.children.length > 1) {
                                adminFeedbackList.removeChild(adminFeedbackList.lastChild);
                            }

                            if (items.length === 0) {
                                var emptyLi = document.createElement('li');
                                emptyLi.className = 'px-3 py-2 small text-muted';
                                emptyLi.textContent = 'No feedback yet.';
                                adminFeedbackList.appendChild(emptyLi);
                                return;
                            }

                            items.forEach(function (f) {
                                var li = document.createElement('li');
                                li.className = 'px-0 py-0 small';

                                var a = document.createElement('a');
                                a.href = 'feedback.php';
                                a.className = 'd-block px-3 py-2 text-reset text-decoration-none' + (f.is_read ? '' : ' fw-semibold notif-unread');

                                var topRow = document.createElement('div');
                                topRow.className = 'd-flex justify-content-between';

                                var spanTitle = document.createElement('span');
                                spanTitle.textContent = (f.type || 'Feedback') + ' from ' + (f.username || 'N/A');

                                var spanTime = document.createElement('span');
                                spanTime.className = 'text-muted';
                                spanTime.style.fontSize = '0.75rem';
                                spanTime.textContent = f.created_at || '';

                                topRow.appendChild(spanTitle);
                                topRow.appendChild(spanTime);

                                var msgDiv = document.createElement('div');
                                msgDiv.className = 'text-muted';
                                msgDiv.style.fontSize = '0.8rem';
                                msgDiv.textContent = f.message || '';

                                a.appendChild(topRow);
                                a.appendChild(msgDiv);

                                li.appendChild(a);
                                adminFeedbackList.appendChild(li);
                            });
                        })
                        .catch(function () {
                            // ignore errors
                        });
                }

                refreshAdminFeedback();
                setInterval(refreshAdminFeedback, 5000);
            }
        });
    </script>
</body>
</html>

