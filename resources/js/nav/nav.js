window.hide_menus = function() {
    let nav_links = document.querySelectorAll('.nav-link');
    nav_links.forEach(function(link) {
        if(link.__x.$data) {
            if(link.__x.$data.sub_menu) {
                link.__x.$data.sub_menu = false;
            }
            if(link.__x.$data.sub_menu_2) {
                link.__x.$data.sub_menu_2 = false;
            }
        }
    })
}
