$(document).ready ->
    window.vm = new Vue
        el: '#app',

        data: ->
            _.extend data,
                showform: not(data['verify_captcha'] or data['verify_result'].length)
                disabled: true
        methods:
            verifyCaptcha: ->
                this.disabled = false if grecaptcha.getResponse()

            handleClick: (event) ->
                event.preventDefault() if this.disabled

            dateFormat: (date) ->
                (new Date(date)).toLocaleDateString 'RU-ru'

    window.verifyCaptcha = ->
        vm.verifyCaptcha()