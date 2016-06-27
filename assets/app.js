$(document).ready(function() {
    var vm = new Vue({
        el: '#app',
        data: {
            disabled: true,
            captchaInput: '',
            showform: false,
            hidemore: true
        },
        methods: {
            handleClick: function (event) {
                if (this.disabled) {
                    event.preventDefault();
                }
            },
            toggleForm: function () {
                event.preventDefault();
                this.showform = !this.showform;
                this.hidemore = !this.hidemore;
            }
        },
        computed: {
            disabled: function () {
                return this.captchaInput.length < 6;
            }
        }
    }); 
});