if(document.URL.match(/loans/)) {


    window.loans = function() {

        return {

            init() {
                this.get_loans();
            },
            get_loans() {
                console.log('working');
            }

        }

    }

}
