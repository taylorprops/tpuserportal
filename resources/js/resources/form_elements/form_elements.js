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


window.show_file_names = function (target, remove = true) {

    let files = target.files;
    let id = target.id || new Date().getTime() * 100;
    document.querySelector('.file-names.' + id).innerHTML = '';
    for (var i = 0; i < files.length; i++) {
        let file_name = truncate_string(files[i].name, 70);
        let html = ' \
        <div class="flex justify-start mt-1">';
        if (remove == true) {
            html += '<div> \
                    <a href="javascript:void(0)" @click="remove_file(\''+ id + '\', ' + i + ')"><i class="fal fa-times text-red-600"></i></a> \
                </div>';
        }
        html += '<div class="ml-3 w-full">' + file_name + '</div> \
        </div>';
        document.querySelector('.file-names.' + id).insertAdjacentHTML('beforeend', html);
    }
}

window.remove_file = function (id, index) {

    let dt = new DataTransfer();
    let input = document.querySelector('#' + id);
    let files = input.files;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (index !== i) {
            dt.items.add(file);
        }
    }

    input.files = dt.files;

    setTimeout(function () {
        this.show_file_names(input);
    }, 300);

}


window.dropdown = function (ele) {

    return {
        ele: ele,
        options: [],
        selected: [],
        show: false,
        init() {
            this.loadOptions();
        },
        open() { this.show = true },
        close() { this.show = false },
        isOpen() { return this.show === true },
        select(index, event) {

            if (!this.options[index].selected) {

                this.options[index].selected = true;
                this.options[index].element = event.target;
                this.selected.push(index);

            } else {
                this.selected.splice(this.selected.lastIndexOf(index), 1);
                this.options[index].selected = false
            }
        },
        remove(index, option) {
            this.options[option].selected = false;
            this.selected.splice(index, 1);


        },
        loadOptions() {
            console.log('working');
            const options = ele.options;

            for (let i = 0; i < options.length; i++) {
                this.options.push({
                    value: options[i].value,
                    text: options[i].innerText,
                    selected: options[i].getAttribute('selected') != null ? options[i].getAttribute('selected') : false
                });
            }

        },

        selectedValues() {
            return this.selected.map((option) => {
                return this.options[option].value;
            })
        },

    }
}
