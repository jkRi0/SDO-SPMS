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
                    lengthChange: (tbl && tbl.id === 'transactionsTable') ? false : true,
                    columnDefs: [{ orderable: false, targets: -1 }],
                    // Default sort: Created column (index 5) descending
                    order: [[5, 'desc']],
                    language: { searchPlaceholder: "Search...", search: "" }
                });
            });

            if (window.jQuery && jQuery.fn && jQuery.fn.dataTable) {
                if (!window.__txDeptFilterInitialized) {
                    window.__txDeptFilterInitialized = true;
                    jQuery.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                        if (!settings || settings.nTable == null || settings.nTable.id !== 'transactionsTable') {
                            return true;
                        }

                        var select = document.getElementById('transactionsDeptFilter');
                        if (!select) {
                            select = null;
                        }

                        var stageSelect = document.getElementById('transactionsStageFilter');
                        if (!stageSelect) {
                            stageSelect = null;
                        }

                        if (!select && !stageSelect) {
                            return true;
                        }

                        var dept = select ? String(select.value || '') : '';
                        var stage = stageSelect ? String(stageSelect.value || '') : '';

                        var rowNode = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
                        if (!rowNode || !rowNode.dataset) {
                            return true;
                        }

                        var okDept = true;
                        if (dept !== '') {
                            var nextDept = String(rowNode.dataset.nextDept || '');
                            var statusDept = String(rowNode.dataset.statusDept || '');
                            okDept = (nextDept === dept || statusDept === dept);
                        }

                        var okStage = true;
                        if (stage !== '') {
                            okStage = String(rowNode.dataset.stage || '') === stage;
                        }

                        return okDept && okStage;
                    });
                }

                var filterWrap = document.getElementById('transactionsDeptFilterWrap');
                var filterSelect = document.getElementById('transactionsDeptFilter');
                var stageWrap = document.getElementById('transactionsStageFilterWrap');
                var stageSelect = document.getElementById('transactionsStageFilter');
                var dtFilter = document.getElementById('transactionsTable_filter');
                var searchSlot = document.getElementById('transactionsSearchSlot');

                if (dtFilter) {
                    dtFilter.classList.add('m-0');
                    dtFilter.classList.add('p-0');
                    dtFilter.classList.add('d-flex');
                    dtFilter.classList.add('align-items-center');

                    if (searchSlot && !searchSlot.contains(dtFilter)) {
                        searchSlot.appendChild(dtFilter);
                    }
                }

                if (filterWrap && filterSelect) {
                    filterWrap.classList.remove('d-none');
                    filterWrap.classList.add('d-inline-flex');
                    filterWrap.classList.add('align-items-center');
                    filterSelect.addEventListener('change', function () {
                        if (jQuery.fn.dataTable.isDataTable('#transactionsTable')) {
                            jQuery('#transactionsTable').DataTable().draw();
                        }
                    });
                }

                if (stageWrap && stageSelect) {
                    stageWrap.classList.remove('d-none');
                    stageWrap.classList.add('d-inline-flex');
                    stageWrap.classList.add('align-items-center');

                    stageSelect.addEventListener('change', function () {
                        if (jQuery.fn.dataTable.isDataTable('#transactionsTable')) {
                            jQuery('#transactionsTable').DataTable().draw();
                        }
                    });
                }
            }

            // Auto-refresh supplier notifications in header bell
            var notifBadge = document.getElementById('notifBadge');
            var notifList = document.getElementById('notifList');
            if (notifBadge && notifList) {
                function refreshNotifications() {
                    if (document.visibilityState !== 'visible') {
                        return;
                    }

                    fetch('api/api_notifications.php', { cache: 'no-store' })
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
                setInterval(refreshNotifications, (window.POLL_INTERVALS && window.POLL_INTERVALS.HEADER_NOTIFICATIONS) || 5000);
            }

            // Auto-refresh department notifications in header bell
            var deptNotifBadge = document.getElementById('deptNotifBadge');
            var deptNotifList = document.getElementById('deptNotifList');
            if (deptNotifBadge && deptNotifList) {
                function refreshDeptNotifications() {
                    if (document.visibilityState !== 'visible') {
                        return;
                    }

                    fetch('api/api_dept_notifications.php', { cache: 'no-store' })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data || !data.success) return;

                            var unread = data.unread_count || 0;
                            if (unread > 0) {
                                deptNotifBadge.style.display = 'inline-block';
                                deptNotifBadge.textContent = unread > 9 ? '9+' : unread;
                            } else {
                                deptNotifBadge.style.display = 'none';
                                deptNotifBadge.textContent = '';
                            }

                            var items = data.notifications || [];

                            while (deptNotifList.children.length > 1) {
                                deptNotifList.removeChild(deptNotifList.lastChild);
                            }

                            if (items.length === 0) {
                                var emptyLi = document.createElement('li');
                                emptyLi.className = 'px-3 py-2 small text-muted';
                                emptyLi.textContent = 'No notifications yet.';
                                deptNotifList.appendChild(emptyLi);
                                return;
                            }

                            items.forEach(function (n) {
                                var li = document.createElement('li');
                                li.className = 'px-0 py-0 small';

                                var a = document.createElement('a');
                                a.href = n.link ? ('dept_notification_open.php?id=' + encodeURIComponent(n.id)) : '#';
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
                                deptNotifList.appendChild(li);
                            });
                        })
                        .catch(function () {
                        });
                }

                refreshDeptNotifications();
                setInterval(refreshDeptNotifications, (window.POLL_INTERVALS && window.POLL_INTERVALS.HEADER_DEPT_NOTIFICATIONS) || 5000);
            }

            // Auto-refresh admin feedback dropdown (admin role only)
            var adminFeedbackBadge = document.getElementById('adminFeedbackBadge');
            var adminFeedbackList = document.getElementById('adminFeedbackList');
            if (adminFeedbackBadge && adminFeedbackList) {
                function refreshAdminFeedback() {
                    if (document.visibilityState !== 'visible') {
                        return;
                    }

                    fetch('api/api_admin_feedback.php', { cache: 'no-store' })
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
                setInterval(refreshAdminFeedback, (window.POLL_INTERVALS && window.POLL_INTERVALS.HEADER_ADMIN_FEEDBACK) || 5000);
            }
        });
    </script>
</body>
</html>

