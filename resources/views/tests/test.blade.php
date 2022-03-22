<script>

let careers_pages = ['careers', '100-commission', '85-commission'];

let matched = careers_pages.filter(item => {
    return document.URL.match(item);
});

if(matched.length > 0) {
    console.log(matched);
}

</script>
