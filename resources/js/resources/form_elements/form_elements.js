const allRanges = document.querySelectorAll(".range-wrap");
allRanges.forEach(wrap => {
    const range = wrap.querySelector(".range");
    const bubble = wrap.querySelector(".bubble");

    range.addEventListener("input", () => {
        setBubble(range, bubble);
    });
    setBubble(range, bubble);
});

function setBubble(range, bubble) {
    const val = range.value;
    const min = range.min ? range.min : 0;
    const max = range.max ? range.max : 100;
    const newVal = Number(((val - min) * 100) / (max - min));
    bubble.innerHTML = val;

    // Sorta magic numbers based on size of the native UI thumb
    bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
}


window.show_file_names = function (target) {
    let files = target.files;
    let id = target.id;
    document.querySelector('.file-names').innerHTML = '';
    for (var i = 0; i < files.length; i++) {
        let file_name = truncate_string(files[i].name, 70);
        let html = ' \
        <div class="flex justify-start"> \
            <div> \
                <a href="javascript:void(0)" @click="remove_file(\''+id+'\', '+i+')"><i class="fal fa-times text-red-600"></i></a> \
            </div> \
            <div class="ml-3 w-full">'+file_name+'</div> \
        </div>';
        document.querySelector('.file-names').insertAdjacentHTML('beforeend', html);
    }
}

window.remove_file = function(id, index) {

    let dt = new DataTransfer();
    let input = document.querySelector('#'+id);
    let files = input.files;
    console.log(files);
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (index !== i) {
            dt.items.add(file);
        }
    }

    input.files = dt.files;

    setTimeout(function() {
        this.show_file_names(input);
    }, 300);

}


