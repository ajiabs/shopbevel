define(
  [
    'jquery',
    'selection-cache'
  ],
  function($, _$) {

    function app() {
      var app = this;


      var quick_step_length = 150;
      var step_length = 1500;

      // To publicly expose a function add it to this obj
      var pub = {};

      pub.init = function() {
        app.bindEventHandlers();
      }

      app.bindEventHandlers = function() {
        var events = {
          '.screen.one .btn':                 {e: 'click', f: app.showScreenTwo},
          '.screen.two .continue .btn':       {e: 'click', f: app.showBezelOverlap},
          '.screen.two .bezel-explain .btn':  {e: 'click', f: app.showTrunkshows},
          '#attend-button':                   {e: 'click', f: app.validateTrunkSelection},
          '#supply-email':                    {e: 'focus', f: app.clearEmailInvalid}
        }

        $.each(events, function(selector, action) {
          _$(selector).on(action.e, action.f);
        });
      }

      app.showScreenTwo = function() {
        _$('#container').addClass('big');
        _$('.screen.one').removeClass('visible');
        _$('.screen.two').addClass('visible');

        // Initial show icons
        _$('#intro-icons .column').each(function(i, el) {
          setTimeout(function() {
            $(el).addClass('intro');
          }, (i + 1) * quick_step_length);
        });
        setTimeout(app.sequentiallyShowSteps, 6 * quick_step_length);
      }

      app.clearCurrentColumns = function() {
        _$('#intro-icons .column').removeClass('current');
      }

      app.sequentiallyShowSteps = function() {
        var steps = new Array();
        _$('#intro-icons .column').each(function(i, el) {
          // Explain eachs tep
          steps.push(function() {
            _$('#intro-icons .column').removeClass('current');
            $(el).addClass('active current');
          });
        });

        // Finish fade-in
        steps.push(function() {
          _$('#intro-icons').addClass('interactive');
          app.clearCurrentColumns();
          _$('.screen.two .continue').addClass('visible');
        });

        app.sequenceSteps(steps, step_length);
      }

      app.showBezelOverlap = function() {
        _$('#intro-icons').removeClass('interactive');
        _$('.screen.two .continue').removeClass('visible');
        _$('.screen.two .bezel-explain').addClass('visible');

        setTimeout(function() {
          _$('.screen.two .subtitle .one').removeClass('visible');
          _$('.screen.two .subtitle .two').addClass('visible');
        }, quick_step_length);

        app.sequenceSteps([
          function() {
            _$('#bezel-column .description').html(_$('#bezel-column .description').data('swap-text'));
            app.clearCurrentColumns();
            _$('#bezel-column').addClass('swap active current');
          },
          function() {
            _$('#sale-column .description').html(_$('#sale-column .description').data('swap-text'));
            app.clearCurrentColumns();
            _$('#sale-column').addClass('swap active current');
          }, 
          function() {
            _$('#ship-column .description').html(_$('#sale-column .description').data('swap-text'));
            app.clearCurrentColumns();
            _$('#ship-column').addClass('swap active current');
          },
          function() {
            app.clearCurrentColumns();
          }
        ], step_length);
      }

      app.showTrunkshows = function() {
        _$('#container').removeClass('big').addClass('tall');
        _$('.screen.one').removeClass('visible');
        _$('.screen.two').removeClass('visible');
        _$('.screen.three').addClass('visible');

        _$('#trunkshows .checkbox').on('click', function() {
          $(this).toggleClass('checked');
        });
      }

      app.clearEmailInvalid = function() {
        _$('#supply-email').removeClass('invalid' );
      }

      app.validateTrunkSelection = function() {

        // Check for email
        _$('#supply-email').toggleClass('invalid', _$('#supply-email').val() == '');
        if(_$('#supply-email').val() == '') { return false; }

        var selected_trunks = new Array();
        $('#trunkshows .checkbox.checked').each(function(i, el) {
          selected_trunks.push($(el).data('unique'));
        });

        // Woo valid email
        $.ajax({
          url: 'save-email.php',
          type: 'post',
          data: {
            email: _$('#supply-email').val(),
            referred: _$('#supply-referred').val(),
            trunkshows: selected_trunks
          },

          // Returned success
          success: function() {
            app.showScreenFour();
          }
        });
      }

      app.showScreenFour = function() {
        _$('#container').removeClass('tall');
        _$('.screen.three').removeClass('visible');
        _$('.screen.four').addClass('visible');
      }

      app.sequenceSteps = function(array, pause_length) {
        $.each(array, 
          function(i, f) {
            setTimeout(f, pause_length * i);
          }
        );
      }

      return pub;
    }

    return new app();
  }
);