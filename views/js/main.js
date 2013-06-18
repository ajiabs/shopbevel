requirejs.config({
  paths: {
    'jquery' :          'https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min',
    'selection-cache':  'libs/selection-cache.min',
  }
});

require(
  [
    'jquery',
    'app'
  ], 
  function($, app) {

    // jQuery is ready, lets go
    $(function() {
      app.init();
    });

  }
);