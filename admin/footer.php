        </div> <!-- End Page Content -->
    </div> <!-- End Admin Content -->
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Admin JavaScript -->
    <script>
        $(document).ready(function() {
            // Sidebar toggle functionality
            $('#sidebarToggle').click(function() {
                $('#adminSidebar').toggleClass('collapsed');
                $('#adminContent').toggleClass('expanded');
                $(this).toggleClass('collapsed');
                
                // Update icon
                const icon = $(this).find('i');
                if (icon.hasClass('fa-bars')) {
                    icon.removeClass('fa-bars').addClass('fa-times');
                } else {
                    icon.removeClass('fa-times').addClass('fa-bars');
                }
            });
            
            // Mobile sidebar toggle
            $(window).resize(function() {
                if ($(window).width() <= 768) {
                    $('#adminSidebar').removeClass('collapsed');
                    $('#adminContent').removeClass('expanded');
                    $('#sidebarToggle').removeClass('collapsed');
                }
            });
            
            // Mobile sidebar toggle
            if ($(window).width() <= 768) {
                $('#sidebarToggle').click(function(e) {
                    e.stopPropagation();
                    $('#adminSidebar').toggleClass('mobile-open');
                });
                
                // Close sidebar when clicking outside
                $(document).click(function(e) {
                    if (!$(e.target).closest('#adminSidebar, #sidebarToggle').length) {
                        $('#adminSidebar').removeClass('mobile-open');
                    }
                });
            }
            
            // Auto-save functionality for forms
            $('.auto-save').on('change', function() {
                const form = $(this).closest('form');
                const formData = form.serialize();
                
                $.ajax({
                    url: form.attr('action') || window.location.href,
                    method: 'POST',
                    data: formData + '&auto_save=1',
                    success: function(response) {
                        if (response.success) {
                            showToast('Автозбереження', 'Зміни збережено', 'success');
                        }
                    }
                });
            });
            
            // Confirm delete actions
            $('.delete-confirm').click(function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const itemName = $(this).data('name') || 'цей елемент';
                
                Swal.fire({
                    title: 'Підтвердження видалення',
                    text: `Ви впевнені, що хочете видалити ${itemName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Так, видалити',
                    cancelButtonText: 'Скасувати'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
            
            // Data tables initialization
            if ($.fn.DataTable) {
                $('.data-table').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/uk.json'
                    },
                    pageLength: 25,
                    order: [[0, 'desc']]
                });
            }
            
            // File upload preview
            $('.file-upload-preview').change(function() {
                const file = this.files[0];
                const preview = $(this).siblings('.preview');
                
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.html(`<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">`);
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.empty();
                }
            });
            
            // Theme switcher in admin panel
            $('.theme-switch').click(function() {
                const theme = $(this).data('theme');
                changeAdminTheme(theme);
            });
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Toast notifications
        function showToast(title, message, type = 'info') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: type,
                title: title,
                text: message
            });
        }
        
        // Change admin theme
        function changeAdminTheme(theme) {
            $.ajax({
                url: '../ajax/change_theme.php',
                method: 'POST',
                data: {
                    action: 'change_theme',
                    theme: theme
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
        
        // Auto-refresh for real-time data
        function enableAutoRefresh(selector, interval = 30000) {
            setInterval(function() {
                $(selector).load(window.location.href + ' ' + selector + ' > *');
            }, interval);
        }
        
        // Export functionality
        function exportData(type, table) {
            const url = `export.php?type=${type}&table=${table}`;
            window.open(url, '_blank');
        }
        
        // Bulk actions
        function handleBulkAction(action, items) {
            if (items.length === 0) {
                showToast('Помилка', 'Оберіть елементи для дії', 'error');
                return;
            }
            
            Swal.fire({
                title: 'Підтвердження',
                text: `Застосувати дію "${action}" до ${items.length} елементів?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Так',
                cancelButtonText: 'Скасувати'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'bulk_actions.php',
                        method: 'POST',
                        data: {
                            action: action,
                            items: items
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                showToast('Помилка', response.message, 'error');
                            }
                        }
                    });
                }
            });
        }
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            // Optionally send to logging service
        });
        
        // AJAX setup
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                // Add CSRF token if available
                const token = $('meta[name="csrf-token"]').attr('content');
                if (token) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', token);
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 403) {
                    showToast('Помилка', 'Доступ заборонено', 'error');
                } else if (xhr.status === 500) {
                    showToast('Помилка', 'Помилка сервера', 'error');
                } else {
                    showToast('Помилка', 'Сталася помилка: ' + error, 'error');
                }
            }
        });
    </script>
</body>
</html>
