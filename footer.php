    </div> <!-- /.container -->

    <script src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery/jquery-3.7.1.min.js"></script>
    <script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables/responsive.bootstrap5.min.js"></script>
    <script src="assets/vendor/jspdf/jspdf.umd.min.js"></script>
    <script src="assets/vendor/jspdf-autotable/jspdf.plugin.autotable.min.js"></script>
    <script src="assets/vendor/xlsx/xlsx.full.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-dismiss success alerts after 5 seconds, but NOT error alerts
            setTimeout(function() {
                const successAlerts = document.querySelectorAll('.alert-success:not(.alert-danger)');
                successAlerts.forEach(function(alert) {
                    // Add fade out effect
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 5000);
            
            function setConnDot(el, state) {
                if (!el) return;
                el.classList.remove('conn-dot--ok');
                el.classList.remove('conn-dot--bad');
                el.classList.remove('conn-badge--ok');
                el.classList.remove('conn-badge--bad');
                if (state === true) {
                    el.classList.add('conn-dot--ok');
                    el.classList.add('conn-badge--ok');
                } else if (state === false) {
                    el.classList.add('conn-dot--bad');
                    el.classList.add('conn-badge--bad');
                }
            }

            function fetchWithTimeout(url, ms, opts) {
                opts = opts || {};
                if (!('AbortController' in window)) {
                    return fetch(url, opts);
                }
                const controller = new AbortController();
                const t = setTimeout(function () {
                    try { controller.abort(); } catch (e) {}
                }, ms);
                const merged = Object.assign({}, opts, { signal: controller.signal });
                return fetch(url, merged).finally(function () {
                    clearTimeout(t);
                });
            }

            var connLocalDot = document.getElementById('connLocalDot');
            var connInternetDot = document.getElementById('connInternetDot');

            function checkConnectivity() {
                if (connLocalDot) {
                    fetchWithTimeout('api/api_ping.php', 3000, { cache: 'no-store' })
                        .then(function (res) {
                            setConnDot(connLocalDot, !!(res && res.ok));
                        })
                        .catch(function () {
                            setConnDot(connLocalDot, false);
                        });
                }

                if (connInternetDot) {
                    if (navigator.onLine === false) {
                        setConnDot(connInternetDot, false);
                        return;
                    }
                    fetchWithTimeout('https://www.gstatic.com/generate_204', 3000, { mode: 'no-cors', cache: 'no-store' })
                        .then(function () {
                            setConnDot(connInternetDot, true);
                        })
                        .catch(function () {
                            setConnDot(connInternetDot, false);
                        });
                }
            }

            checkConnectivity();
            window.addEventListener('online', checkConnectivity);
            window.addEventListener('offline', checkConnectivity);
            setInterval(function () {
                if (window.SMART_POLLING_ENABLED) {
                    if (document.visibilityState !== 'visible' || !document.hasFocus()) {
                        return;
                    }
                }
                checkConnectivity();
            }, window.POLL_INTERVALS.CONNECTIVITY);

            async function __readAsDataUrl(url) {
                try {
                    const res = await fetch(url, { cache: 'no-store' });
                    if (!res.ok) return null;
                    const blob = await res.blob();
                    return await new Promise(function (resolve) {
                        const reader = new FileReader();
                        reader.onloadend = function () { resolve(reader.result || null); };
                        reader.onerror = function () { resolve(null); };
                        reader.readAsDataURL(blob);
                    });
                } catch (e) {
                    return null;
                }
            }

            async function __getImageSize(dataUrl) {
                return await new Promise(function (resolve) {
                    if (!dataUrl) return resolve(null);
                    var img = new window.Image();
                    img.onload = function () {
                        resolve({ width: img.naturalWidth || 0, height: img.naturalHeight || 0 });
                    };
                    img.onerror = function () { resolve(null); };
                    img.src = dataUrl;
                });
            }

            function __fitRect(srcW, srcH, maxW, maxH) {
                if (!srcW || !srcH || !maxW || !maxH) {
                    return { w: maxW, h: maxH };
                }
                var ratio = Math.min(maxW / srcW, maxH / srcH);
                return { w: srcW * ratio, h: srcH * ratio };
            }

            async function exportTransactionsPdf() {
                if (!window.jspdf || !window.jspdf.jsPDF) {
                    alert('PDF library not loaded.');
                    return;
                }
                if (!(window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable('#transactionsTable'))) {
                    alert('Transactions table is not ready.');
                    return;
                }

                const dt = jQuery('#transactionsTable').DataTable();
                const nodes = dt.rows({ search: 'applied' }).nodes().toArray();

                function __normText(s) {
                    return String(s || '')
                        .replace(/\u00a0/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                }

                function __normAmount(s) {
                    var out = __normText(s);
                    out = out
                        .replace(/\s*,\s*/g, ',')
                        .replace(/\s*\.\s*/g, '.')
                        .replace(/(\d)\s+(?=[\d,.])/g, '$1');

                    // Keep numbers only (optionally with '.' and '-')
                    // Example inputs can contain currency symbols or other glyphs.
                    var numeric = out.replace(/[^0-9.\-]/g, '');
                    if (numeric === '' || numeric === '-' || numeric === '.' || numeric === '-.') {
                        return '';
                    }

                    var num = Number(numeric);
                    if (!Number.isFinite(num)) {
                        // Fallback: return the digits-only string
                        return numeric;
                    }

                    // Format as plain number with commas; keep decimals only if present in source
                    var hasDecimal = numeric.indexOf('.') !== -1;
                    if (hasDecimal) {
                        var parts = numeric.split('.');
                        var decLen = parts[1] ? Math.min(parts[1].length, 2) : 0;
                        return num.toLocaleString(undefined, {
                            minimumFractionDigits: decLen,
                            maximumFractionDigits: decLen,
                        });
                    }
                    return Math.trunc(num).toLocaleString(undefined);
                }

                const rows = [];
                nodes.forEach(function (tr) {
                    if (!tr || !tr.cells || tr.cells.length < 6) return;
                    const po = __normText(tr.cells[0].textContent);
                    const supplier = __normText(tr.cells[1].textContent);
                    const program = __normText(tr.cells[2].textContent);
                    const amount = __normAmount(tr.cells[3].textContent);
                    const status = __normText(tr.cells[4].textContent);
                    const created = __normText(tr.cells[5].textContent);
                    if (po === '' && supplier === '' && program === '') return;
                    rows.push([po, supplier, program, amount, status, created]);
                });

                if (rows.length === 0) {
                    alert('No rows to export (check filters/search).');
                    return;
                }

                if (window.__txPdfHeaderDataUrl === undefined) {
                    window.__txPdfHeaderDataUrl = await __readAsDataUrl('assets/images/header.jpg');
                }
                if (window.__txPdfFooterDataUrl === undefined) {
                    window.__txPdfFooterDataUrl = await __readAsDataUrl('assets/images/footer.jpg');
                }

                const headerDataUrl = window.__txPdfHeaderDataUrl;
                const footerDataUrl = window.__txPdfFooterDataUrl;

                const doc = new window.jspdf.jsPDF('p', 'mm', 'a4');
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();

                const leftMargin = 10;
                const rightMargin = 10;
                const headerY = 6;
                const footerBottomPad = 6;

                const maxHeaderW = pageWidth - (leftMargin + rightMargin);
                const maxFooterW = pageWidth - (leftMargin + rightMargin);
                const maxHeaderH = 42;
                const maxFooterH = 22;

                const headerSizePx = await __getImageSize(headerDataUrl);
                const footerSizePx = await __getImageSize(footerDataUrl);

                const headerFit = headerDataUrl && headerSizePx ? __fitRect(headerSizePx.width, headerSizePx.height, maxHeaderW, maxHeaderH) : { w: maxHeaderW, h: 0 };
                const footerFit = footerDataUrl && footerSizePx ? __fitRect(footerSizePx.width, footerSizePx.height, maxFooterW, maxFooterH) : { w: maxFooterW, h: 0 };

                const headerW = headerFit.w;
                const headerH = headerFit.h;
                const headerX = (pageWidth - headerW) / 2;

                const footerW = footerFit.w;
                const footerH = footerFit.h;
                const footerX = (pageWidth - footerW) / 2;
                const footerY = pageHeight - footerH - footerBottomPad;

                const topMargin = headerY + headerH + 7;
                const bottomMargin = footerH + footerBottomPad + 10;

                function drawHeaderFooter() {
                    if (headerDataUrl) {
                        doc.addImage(headerDataUrl, 'JPEG', headerX, headerY, headerW, headerH);
                    }
                    if (footerDataUrl) {
                        doc.addImage(footerDataUrl, 'JPEG', footerX, footerY, footerW, footerH);
                    }
                    doc.setDrawColor(0);
                    doc.setLineWidth(0.2);
                    if (headerH > 0) {
                        doc.line(leftMargin, headerY + headerH + 1.5, pageWidth - rightMargin, headerY + headerH + 1.5);
                    }
                    if (footerH > 0) {
                        doc.line(leftMargin, footerY - 1.5, pageWidth - rightMargin, footerY - 1.5);
                    }
                }

                doc.setFontSize(11);
                doc.setTextColor(30, 30, 30);
                const title = '';
                doc.text(title, leftMargin, topMargin - 6);

                doc.autoTable({
                    startY: topMargin,
                    head: [["PO #", "Supplier", "Program Title", "Amount", "Current Status", "Created"]],
                    body: rows,
                    theme: 'grid',
                    margin: { top: topMargin, bottom: bottomMargin, left: leftMargin, right: rightMargin },
                    tableWidth: 'auto',
                    styles: { fontSize: 8, cellPadding: { top: 2, right: 2, bottom: 2, left: 2 }, overflow: 'linebreak', valign: 'middle' },
                    headStyles: { fillColor: [245, 247, 250], textColor: [20, 20, 20], fontStyle: 'bold', overflow: 'linebreak' },
                    columnStyles: {
                        0: { cellWidth: 24 },
                        1: { cellWidth: 28 },
                        2: { cellWidth: 44 },
                        3: { cellWidth: 24, halign: 'right' },
                        4: { cellWidth: 40 },
                        5: { cellWidth: 26 },
                    },
                    didParseCell: function (data) {
                        if (!data || !data.cell) return;
                        if (data.section === 'body') {
                            if (data.column && data.column.index === 0) {
                                data.cell.styles.overflow = 'hidden';
                            }
                            if (data.column && data.column.index === 3) {
                                data.cell.styles.overflow = 'hidden';
                                data.cell.styles.fontSize = 7;
                            }
                            if (data.column && data.column.index === 5) {
                                data.cell.styles.overflow = 'hidden';
                                data.cell.styles.fontSize = 7;
                            }
                        }
                    },
                    didDrawPage: function () {
                        drawHeaderFooter();
                    },
                });

                const pdfBlob = doc.output('blob');
                const url = URL.createObjectURL(pdfBlob);
                window.open(url, '_blank');
            }

            function exportTransactionsExcel() {
                if (!window.XLSX) {
                    alert('Excel library not loaded.');
                    return;
                }
                if (!(window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.isDataTable('#transactionsTable'))) {
                    alert('Transactions table is not ready.');
                    return;
                }

                const dt = jQuery('#transactionsTable').DataTable();
                const nodes = dt.rows({ search: 'applied' }).nodes().toArray();

                function __normText(s) {
                    return String(s || '')
                        .replace(/\u00a0/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim();
                }

                function __normAmount(s) {
                    var out = __normText(s);
                    out = out
                        .replace(/\s*,\s*/g, ',')
                        .replace(/\s*\.\s*/g, '.')
                        .replace(/(\d)\s+(?=[\d,.])/g, '$1');

                    var numeric = out.replace(/[^0-9.\-]/g, '');
                    if (numeric === '' || numeric === '-' || numeric === '.' || numeric === '-.') {
                        return '';
                    }

                    var num = Number(numeric);
                    if (!Number.isFinite(num)) {
                        return numeric;
                    }

                    var hasDecimal = numeric.indexOf('.') !== -1;
                    if (hasDecimal) {
                        var parts = numeric.split('.');
                        var decLen = parts[1] ? Math.min(parts[1].length, 2) : 0;
                        return Number(num.toFixed(decLen));
                    }
                    return Math.trunc(num);
                }

                const header = ["PO #", "Supplier", "Program Title", "Amount", "Current Status", "Created"];
                const data = [header];

                nodes.forEach(function (tr) {
                    if (!tr || !tr.cells || tr.cells.length < 6) return;
                    const po = __normText(tr.cells[0].textContent);
                    const supplier = __normText(tr.cells[1].textContent);
                    const program = __normText(tr.cells[2].textContent);
                    const amount = __normAmount(tr.cells[3].textContent);
                    const status = __normText(tr.cells[4].textContent);
                    const created = __normText(tr.cells[5].textContent);
                    if (po === '' && supplier === '' && program === '') return;
                    data.push([po, supplier, program, amount, status, created]);
                });

                if (data.length <= 1) {
                    alert('No rows to export (check filters/search).');
                    return;
                }

                const ws = XLSX.utils.aoa_to_sheet(data);
                ws['!cols'] = [
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 28 },
                    { wch: 14 },
                    { wch: 26 },
                    { wch: 18 },
                ];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Transactions');

                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                XLSX.writeFile(wb, 'transactions_' + yyyy + '-' + mm + '-' + dd + '.xlsx');
            }

            window.exportTransactionsPdf = exportTransactionsPdf;
            var pdfBtn = document.getElementById('btnTransactionsPdf');
            if (pdfBtn) {
                pdfBtn.addEventListener('click', function () {
                    exportTransactionsPdf();
                });
            }

            window.exportTransactionsExcel = exportTransactionsExcel;
            var excelBtn = document.getElementById('btnTransactionsExcel');
            if (excelBtn) {
                excelBtn.addEventListener('click', function () {
                    exportTransactionsExcel();
                });
            }

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

                        dept = dept.toLowerCase().trim();
                        stage = stage.toLowerCase().trim();

                        var rowNode = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex].nTr : null;
                        if (!rowNode || !rowNode.dataset) {
                            return true;
                        }

                        var okDept = true;
                        if (dept !== '') {
                            var nextDept = String(rowNode.dataset.nextDept || '').toLowerCase().trim();
                            var statusDept = String(rowNode.dataset.statusDept || '').toLowerCase().trim();
                            okDept = (nextDept === dept || statusDept === dept);
                        }

                        var okStage = true;
                        if (stage !== '') {
                            var rowStage = String(rowNode.dataset.stage || '').toLowerCase().trim();
                            okStage = rowStage === stage;
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
                    if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
                        return;
                    }

                    fetch('api/api_notifications.php', { cache: 'no-store' })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data || !data.success) return;

                            var unread = data.unread_count || 0;
                            if (unread > 0) {
                                notifBadge.style.display = 'inline-block';
                                notifBadge.textContent = String(unread);
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
                setInterval(refreshNotifications, window.POLL_INTERVALS.HEADER_NOTIFICATIONS);
            }

            // Auto-refresh department notifications in header bell
            var deptNotifBadge = document.getElementById('deptNotifBadge');
            var deptNotifList = document.getElementById('deptNotifList');
            if (deptNotifBadge && deptNotifList) {
                function refreshDeptNotifications() {
                    if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
                        return;
                    }

                    fetch('api/api_dept_notifications.php', { cache: 'no-store' })
                        .then(function (res) { return res.json(); })
                        .then(function (data) {
                            if (!data || !data.success) return;

                            var unread = data.unread_count || 0;
                            if (unread > 0) {
                                deptNotifBadge.style.display = 'inline-block';
                                deptNotifBadge.textContent = String(unread);
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
                setInterval(refreshDeptNotifications, window.POLL_INTERVALS.HEADER_DEPT_NOTIFICATIONS);
            }

            // Auto-refresh admin feedback dropdown (admin role only)
            var adminFeedbackBadge = document.getElementById('adminFeedbackBadge');
            var adminFeedbackList = document.getElementById('adminFeedbackList');
            if (adminFeedbackBadge && adminFeedbackList) {
                function refreshAdminFeedback() {
                    if (window.SMART_POLLING_ENABLED && (document.visibilityState !== 'visible' || !document.hasFocus())) {
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
                setInterval(refreshAdminFeedback, window.POLL_INTERVALS.HEADER_ADMIN_FEEDBACK);
            }
        });
    </script>
</body>
</html>

