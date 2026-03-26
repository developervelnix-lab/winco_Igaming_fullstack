<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width , initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script>
        // Global Theme Management System
        const themeToggle = {
            init: function() {
                const savedTheme = localStorage.getItem('admin-theme') || 'dark';
                this.setTheme(savedTheme);
            },
            setTheme: function(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('admin-theme', theme);
                
                // Update toggle icons (using a timeout to ensure DOM is ready if called early)
                const updateIcons = () => {
                    const darkIcons = document.querySelectorAll('.theme-icon-dark');
                    const lightIcons = document.querySelectorAll('.theme-icon-light');
                    if (theme === 'dark') {
                        darkIcons.forEach(i => i.style.display = 'block');
                        lightIcons.forEach(i => i.style.display = 'none');
                    } else {
                        darkIcons.forEach(i => i.style.display = 'none');
                        lightIcons.forEach(i => i.style.display = 'block');
                    }
                };
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', updateIcons);
                } else {
                    updateIcons();
                }
            },
            toggle: function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                this.setTheme(newTheme);
            }
        };
        themeToggle.init();
    </script>
    
    