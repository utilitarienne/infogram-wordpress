/* globals $ jQuery acf */


(function($) {
  var Field = acf.Field.extend({
    type: "infogram",
    events: {
    },
    $control: function() {
      return this.$(".acf-input-wrap");
    },
    $input: function() {
      return this.$('input[type="text"]:first');
    },
    initialize: function() {
      this.render();
    },
    render: function() {
    },
  });

  acf.registerFieldType(Field);

})(jQuery);
