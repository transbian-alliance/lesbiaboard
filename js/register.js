// Load zxcvbn
(function(){var a;a=function(){var a,b;b=document.createElement("script");b.src="//dl.dropbox.com/u/209/zxcvbn/zxcvbn.js";b.type="text/javascript";b.async=!0;a=document.getElementsByTagName("script")[0];return a.parentNode.insertBefore(b,a)};null!=window.attachEvent?window.attachEvent("onload",a):window.addEventListener("load",a,!1)}).call(this);

$(function () {
  function submitState(enabled) {
    $('[type=submit]').attr('disabled', !enabled)
  }
  submitState(false)
  $('#pw, #un').val("")
  $('#pw2, [name=repeatpassword]').after('<div id="note">')
  $('#pw, #password').on('input', function self() {
    if (typeof zxcvbn !== 'function') {
      submitState(true)
      var that = this
      setTimeout(function () {
         self.call(that)
      }, 50)
    }
    if (!$(this).val()) {
      submitState(!$('#pw2').length)
      $('#note').html("")
      return
    }
    // abxd, acmlmboard, acmlm are special words that could happen
    // considering it's the name of this software
    var result = zxcvbn($(this).val(), ['abxd', 'acmlmboard', 'acmlm'])
    var message = ""
    submitState(true)
    if (result.score <= 2) {
      if (result.score <= 1) {
        if (result.score)
          message = 'Your password is too dangerous. '
        else
          message = 'Your password is unbelievably dangerous. '
        submitState(false)
      }
      else
        message = 'Your password may be dangerous. '
      var time = result.crack_time_display
      if (time === 'instant')
          message += 'It could be guessed automatically almost instantly.'
      else
          message += 'It would take ' + time + ' to guess it.'
    }
    $('#note').html('<small>' + message + '</small>')
  })
})
