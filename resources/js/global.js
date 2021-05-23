window.show_file_names = function(input) {
    let files = input.files;

    for (var i = 0; i < files.length; i++) {
        document.querySelector('.file-names').innerHTML = files[i].name;
    }
}
